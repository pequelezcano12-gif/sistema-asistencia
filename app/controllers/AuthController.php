<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/Mailer.php';

class AuthController {
    private Usuario $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->model = new Usuario();
        Security::setSecurityHeaders();
    }

    // ── INDEX (landing page) ─────────────────────────────────────────────────
    public function index(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }
        require __DIR__ . '/../views/auth/index.php';
    }

    // ── LOGIN ────────────────────────────────────────────────────────────────
    public function login(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $cedula   = trim($_POST['cedula'] ?? '');
            $password = $_POST['password'] ?? '';
            $esAdmin  = ($cedula === 'admin');

            Security::checkRateLimit($cedula);

            // Validación de longitud de contraseña
            // Admin puede usar cualquier contraseña, los demás mínimo 8 caracteres
            if (!$esAdmin && strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (empty($cedula) || empty($password)) {
                $error = 'Completá todos los campos.';
            } elseif (!$esAdmin && !Security::validarCedula(strtoupper($cedula))) {
                $error = 'Número de cédula inválido.';
            } else {
                $buscarCedula = $esAdmin ? 'admin' : strtoupper($cedula);
                $user = $this->model->findByCedula($buscarCedula);

                if ($user && password_verify($password, $user['password'])) {
                    if (!$user['activo']) {
                        $error = 'Tu cuenta está desactivada. Contactá al administrador.';
                    } elseif (!$user['email_verificado']) {
                        $_SESSION['pendiente_verificacion_id'] = $user['id'];
                        header('Location: ' . BASE_URL . '/verificar-email'); exit;
                    } else {
                        Security::clearAttempts($cedula);
                        $this->iniciarSesion($user);
                    }
                } else {
                    Security::registerFailedAttempt($cedula);
                    $error = 'Cédula o contraseña incorrectos.';
                    usleep(random_int(200000, 500000));
                }
            }
        }
        require __DIR__ . '/../views/auth/login.php';
    }

    // ── REGISTRO — PASO 1: detectar cédula ──────────────────────────────────
    public function registrarse(): void {
        $error = null; $info = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $cedula = strtoupper(trim($_POST['cedula'] ?? ''));

            if (!Security::validarCedula($cedula)) {
                $error = 'Número de cédula inválido.';
            } else {
                // Verificar que no tenga ya cuenta activa
                $existeUsuario = $this->model->findByCedula($cedula);
                if ($existeUsuario && $existeUsuario['email_verificado']) {
                    $error = 'Ya existe una cuenta con esa cédula. ¿Olvidaste tu contraseña?';
                } else {
                    // No existe o es pre-registro sin completar → detectar tipo y continuar
                    $tipo = $this->detectarTipoPorCedula($cedula);
                    if ($tipo === 'no_encontrado') {
                        $error = 'La cédula ingresada no está registrada en el sistema. Contactá al administrador.';
                    } else {
                        $token = Security::generateToken();
                        $db = getDB();
                        $db->prepare(
                            "INSERT INTO registro_tokens (cedula, token, datos_json, expira_en)
                             VALUES (:c, :t, :d, NOW() + INTERVAL '30 minutes')"
                        )->execute([':c'=>$cedula, ':t'=>$token, ':d'=>json_encode(['tipo'=>$tipo])]);
                        header('Location: ' . BASE_URL . '/registro/completar?token=' . $token); exit;
                    }
                }
            }
        }
        require __DIR__ . '/../views/auth/registro_cedula.php';
    }

    // ── REGISTRO — PASO 2: completar datos ──────────────────────────────────
    public function registrarCompletar(): void {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        $db    = getDB();

        $st = $db->prepare(
            "SELECT * FROM registro_tokens WHERE token=:t AND usado=FALSE AND expira_en > NOW()"
        );
        $st->execute([':t' => $token]);
        $reg = $st->fetch();

        if (!$reg) {
            $_SESSION['error'] = 'El enlace de registro expiró o es inválido. Iniciá el proceso nuevamente.';
            header('Location: ' . BASE_URL . '/registrarse'); exit;
        }

        $datos = json_decode($reg['datos_json'], true);
        $tipo  = $datos['tipo'];
        $cedula = $reg['cedula'];
        $error  = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();

            $email    = strtolower(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $password2= $_POST['password2'] ?? '';
            $cedPadre = strtoupper(trim($_POST['cedula_padre'] ?? ''));

            // Validaciones
            $error = null;
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email inválido.';
            } elseif (strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $error = 'La contraseña debe tener al menos una mayúscula y un número.';
            } elseif ($password !== $password2) {
                $error = 'Las contraseñas no coinciden.';
            } elseif ($this->model->findByEmail($email)) {
                $error = 'Ese email ya está registrado.';
            }

            // Validación extra para alumnos
            if (!$error && $tipo === 'alumno') {
                if (empty($cedPadre)) {
                    $error = 'Debés ingresar la cédula del padre/madre/tutor.';
                } elseif (!Security::validarCedula($cedPadre)) {
                    $error = 'Cédula del padre/madre inválida.';
                } else {
                    $alumnoCheck = $this->model->getAlumnoByCedula($cedula);
                    if ($alumnoCheck) {
                        $cedReg = trim($alumnoCheck['cedula_padre'] ?? '');
                        if (!empty($cedReg) && strtoupper($cedPadre) !== strtoupper($cedReg)) {
                            $error = 'La cédula del tutor no coincide.';
                        }
                    }
                }
            }

            if (!$error) {
                $alumnoData = $tipo === 'alumno' ? $this->model->getAlumnoByCedula($cedula) : null;

                $rol = match($tipo) {
                    'alumno'               => 'alumno',
                    'profesor_preregistrado' => 'profesor',
                    'padre'                => 'padre',
                    default                => 'alumno',
                };

                $userId = $this->model->createFromRegistro([
                    'cedula'   => $cedula,
                    'email'    => $email,
                    'password' => $password,
                    'rol'      => $rol,
                    'nombre'   => $alumnoData['nombre'] ?? trim($_POST['nombre'] ?? ''),
                    'apellido' => $alumnoData['apellido'] ?? trim($_POST['apellido'] ?? ''),
                ]);

                if ($userId) {
                    if ($tipo === 'alumno' && $alumnoData) {
                        $db->prepare("UPDATE alumnos SET cedula_padre=:cp WHERE id=:id")
                           ->execute([':cp'=>$cedPadre, ':id'=>$alumnoData['id']]);
                    }
                    $db->prepare("UPDATE registro_tokens SET usado=TRUE WHERE token=:t")
                       ->execute([':t' => $token]);
                    $db->prepare("UPDATE usuarios SET email_verificado=TRUE WHERE id=:id")
                       ->execute([':id' => $userId]);

                    $user = $this->model->findById($userId);
                    $this->iniciarSesionDirecta($user, '¡Registro exitoso! Bienvenido/a al sistema.');
                }
                $error = 'Error al crear la cuenta. Intentá nuevamente.';
            }
        }

        require __DIR__ . '/../views/auth/registro_completar.php';
    }

    // ── VERIFICAR EMAIL ──────────────────────────────────────────────────────
    public function verificarEmail(): void {
        $userId = $_SESSION['pendiente_verificacion_id'] ?? null;
        if (!$userId) { header('Location: ' . BASE_URL . '/login'); exit; }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $codigo = trim($_POST['codigo'] ?? '');
            $db = getDB();
            $st = $db->prepare(
                "SELECT * FROM email_verificaciones
                 WHERE usuario_id=:uid AND codigo=:c AND usado=FALSE AND expira_en > NOW()"
            );
            $st->execute([':uid'=>$userId, ':c'=>$codigo]);
            $ver = $st->fetch();

            if ($ver) {
                $db->prepare("UPDATE email_verificaciones SET usado=TRUE WHERE id=:id")
                   ->execute([':id'=>$ver['id']]);
                $db->prepare("UPDATE usuarios SET email_verificado=TRUE WHERE id=:id")
                   ->execute([':id'=>$userId]);

                $user = $this->model->findById($userId);
                unset($_SESSION['pendiente_verificacion_id']);

                // Iniciar sesión automáticamente
                $this->iniciarSesionDirecta($user, '¡Registro exitoso! Bienvenido/a al sistema.');
            }
            $error = 'Código incorrecto o expirado.';
        }
        require __DIR__ . '/../views/auth/verificar_email.php';
    }

    // ── OLVIDÉ MI CONTRASEÑA ─────────────────────────────────────────────────
    public function olvideClave(): void {
        $error = null; $enviado = false;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $cedula = strtoupper(trim($_POST['cedula'] ?? ''));
            Security::checkRateLimit($cedula);

            // Siempre mostrar el mismo mensaje (no revelar si existe)
            $user = $this->model->findByCedula($cedula);
            if ($user && $user['email'] && $user['activo']) {
                $codigo = Security::generateCode();
                $token  = Security::generateToken();
                $db = getDB();
                // Invalidar tokens anteriores
                $db->prepare("UPDATE password_resets SET usado=TRUE WHERE usuario_id=:uid")
                   ->execute([':uid'=>$user['id']]);
                $db->prepare(
                    "INSERT INTO password_resets (usuario_id, token, codigo, expira_en)
                     VALUES (:uid, :t, :c, NOW() + INTERVAL '15 minutes')"
                )->execute([':uid'=>$user['id'], ':t'=>$token, ':c'=>$codigo]);

                Mailer::enviarCodigoReset($user['email'], $user['nombre'], $codigo);
                $_SESSION['reset_token'] = $token;
                // Si el email falla, el código queda en debug_codigo via Mailer
                if (empty($_SESSION['debug_codigo'])) {
                    $_SESSION['debug_codigo'] = $codigo; // fallback siempre visible
                }
            }
            // Siempre redirigir (no revelar si la cédula existe)
            header('Location: ' . BASE_URL . '/reset-password'); exit;
        }
        require __DIR__ . '/../views/auth/olvide_clave.php';
    }

    // ── RESET PASSWORD ───────────────────────────────────────────────────────
    public function resetPassword(): void {
        $token = $_SESSION['reset_token'] ?? '';
        if (!$token) { header('Location: ' . BASE_URL . '/olvide-clave'); exit; }

        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $codigo    = trim($_POST['codigo'] ?? '');
            $password  = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';

            $db = getDB();
            $st = $db->prepare(
                "SELECT * FROM password_resets WHERE token=:t AND codigo=:c AND usado=FALSE AND expira_en > NOW()"
            );
            $st->execute([':t'=>$token, ':c'=>$codigo]);
            $reset = $st->fetch();

            if (!$reset) {
                $error = 'Código incorrecto o expirado.';
            } elseif (strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $error = 'Debe tener al menos una mayúscula y un número.';
            } elseif ($password !== $password2) {
                $error = 'Las contraseñas no coinciden.';
            } else {
                $this->model->updatePassword($reset['usuario_id'], $password);
                $db->prepare("UPDATE password_resets SET usado=TRUE WHERE id=:id")
                   ->execute([':id'=>$reset['id']]);
                unset($_SESSION['reset_token']);
                $_SESSION['success'] = 'Contraseña actualizada correctamente.';
                header('Location: ' . BASE_URL . '/login'); exit;
            }
        }
        require __DIR__ . '/../views/auth/reset_password.php';
    }

    // ── SELECCIÓN DE ROL (docente que también es padre) ──────────────────────
    public function seleccionarRol(): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login'); exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrf();
            $modo = $_POST['modo'] ?? 'profesor';
            $_SESSION['modo_activo'] = in_array($modo, ['profesor','padre']) ? $modo : 'profesor';
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }
        require __DIR__ . '/../views/auth/seleccionar_rol.php';
    }

    // ── LOGOUT ───────────────────────────────────────────────────────────────
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . '/login'); exit;
    }

    // ── CHECK PERMISOS ───────────────────────────────────────────────────────
    public static function check(array $roles = []): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login'); exit;
        }
        if (!empty($roles) && !in_array($_SESSION['user_rol'], $roles)) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php'; exit;
        }
    }

    // ── HELPERS PRIVADOS ─────────────────────────────────────────────────────
    private function iniciarSesion(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_rol']    = $user['rol'];
        $_SESSION['user_nombre'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_cedula'] = $user['cedula'];
        $_SESSION['login_time']  = time();

        getDB()->prepare("UPDATE usuarios SET ultimo_login=NOW() WHERE id=:id")
               ->execute([':id' => $user['id']]);

        if ($user['rol'] === 'profesor' && $this->esTambienPadre($user['cedula'])) {
            $_SESSION['es_tambien_padre'] = true;
            header('Location: ' . BASE_URL . '/seleccionar-rol'); exit;
        }

        // Alumno va a su perfil
        if ($user['rol'] === 'alumno') {
            header('Location: ' . BASE_URL . '/alumnos/perfil'); exit;
        }

        header('Location: ' . BASE_URL . '/dashboard'); exit;
    }

    private function iniciarSesionDirecta(array $user, string $mensaje = ''): void {
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_rol']    = $user['rol'];
        $_SESSION['user_nombre'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_cedula'] = $user['cedula'];
        $_SESSION['login_time']  = time();
        if ($mensaje) $_SESSION['success'] = $mensaje;

        getDB()->prepare("UPDATE usuarios SET ultimo_login=NOW() WHERE id=:id")
               ->execute([':id' => $user['id']]);

        // Redirigir según rol
        if ($user['rol'] === 'alumno') {
            $dest = BASE_URL . '/alumnos/perfil';
        } elseif ($user['rol'] === 'profesor') {
            $dest = BASE_URL . '/asistencia';
        } elseif ($user['rol'] === 'padre') {
            $dest = BASE_URL . '/dashboard';
        } else {
            $dest = BASE_URL . '/dashboard';
        }
        header('Location: ' . $dest); exit;
    }

    private function detectarTipoPorCedula(string $cedula): string {
        $db = getDB();

        // ¿Es alumno registrado?
        $st = $db->prepare("SELECT id FROM alumnos WHERE dni = :c AND activo = TRUE");
        $st->execute([':c' => $cedula]);
        if ($st->fetch()) return 'alumno';

        // ¿Es profesor pre-registrado por admin?
        $st = $db->prepare("SELECT id FROM usuarios WHERE cedula = :c AND rol = 'profesor' AND activo = TRUE");
        $st->execute([':c' => $cedula]);
        if ($st->fetch()) return 'profesor_preregistrado';

        // ¿Es padre/madre/encargado de algún alumno?
        $st = $db->prepare(
            "SELECT id FROM alumnos WHERE (cedula_padre = :c OR cedula_madre = :c) AND activo = TRUE"
        );
        $st->execute([':c' => $cedula]);
        if ($st->fetch()) return 'padre';

        return 'no_encontrado';
    }

    private function esTambienPadre(string $cedula): bool {
        $db = getDB();
        $st = $db->prepare(
            "SELECT COUNT(*) FROM alumnos WHERE (cedula_padre = :c OR cedula_madre = :c) AND activo = TRUE"
        );
        $st->execute([':c' => $cedula]);
        return (int)$st->fetchColumn() > 0;
    }
}
