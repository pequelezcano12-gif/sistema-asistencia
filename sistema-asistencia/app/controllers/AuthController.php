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
            $cedula   = strtoupper(trim($_POST['cedula'] ?? ''));
            $password = $_POST['password'] ?? '';

            Security::checkRateLimit($cedula);

            if (!Security::validarCedula($cedula) || empty($password)) {
                $error = 'Datos inválidos.';
            } else {
                $user = $this->model->findByCedula($cedula);
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
                    // Mensaje genérico para no revelar si la cédula existe
                    $error = 'Cédula o contraseña incorrectos.';
                    // Pequeño delay para dificultar timing attacks
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
                if ($existeUsuario) {
                    $error = 'Ya existe una cuenta con esa cédula. ¿Olvidaste tu contraseña?';
                } else {
                    $tipo = $this->detectarTipoPorCedula($cedula);
                    if ($tipo === 'no_encontrado') {
                        $error = 'La cédula ingresada no está registrada en el sistema. Contactá al administrador.';
                    } else {
                        // Generar token de registro firmado (expira en 30 min)
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
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email inválido.';
            } elseif (strlen($password) < 8) {
                $error = 'La contraseña debe tener al menos 8 caracteres.';
            } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $error = 'La contraseña debe tener al menos una mayúscula y un número.';
            } elseif ($password !== $password2) {
                $error = 'Las contraseñas no coinciden.';
            } elseif ($tipo === 'alumno' && empty($cedPadre)) {
                $error = 'Debés ingresar la cédula del padre/madre/tutor.';
            } elseif ($tipo === 'alumno' && !Security::validarCedula($cedPadre)) {
                $error = 'Cédula del padre/madre inválida.';
            } elseif ($this->model->findByEmail($email)) {
                $error = 'Ese email ya está registrado.';
            } else {
                // Crear usuario con email_verificado=FALSE
                $alumnoData = $tipo === 'alumno'
                    ? $this->model->getAlumnoByCedula($cedula)
                    : null;

                $userId = $this->model->createFromRegistro([
                    'cedula'   => $cedula,
                    'email'    => $email,
                    'password' => $password,
                    'rol'      => $tipo === 'alumno' ? 'alumno' : 'profesor',
                    'nombre'   => $alumnoData['nombre'] ?? '',
                    'apellido' => $alumnoData['apellido'] ?? '',
                ]);

                if ($userId) {
                    // Si es alumno, guardar cédula del padre
                    if ($tipo === 'alumno' && $alumnoData) {
                        $db->prepare("UPDATE alumnos SET cedula_padre=:cp WHERE id=:id")
                           ->execute([':cp'=>$cedPadre, ':id'=>$alumnoData['id']]);
                    }

                    // Marcar token como usado
                    $db->prepare("UPDATE registro_tokens SET usado=TRUE WHERE token=:t")
                       ->execute([':t' => $token]);

                    // Enviar código de verificación
                    $codigo = Security::generateCode();
                    $db->prepare(
                        "INSERT INTO email_verificaciones (usuario_id, codigo, expira_en)
                         VALUES (:uid, :c, NOW() + INTERVAL '30 minutes')"
                    )->execute([':uid'=>$userId, ':c'=>$codigo]);

                    Mailer::enviarCodigoVerificacion($email, $alumnoData['nombre'] ?? $cedula, $codigo);

                    $_SESSION['pendiente_verificacion_id'] = $userId;
                    header('Location: ' . BASE_URL . '/verificar-email'); exit;
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
                unset($_SESSION['pendiente_verificacion_id']);
                $_SESSION['success'] = 'Cuenta verificada. Ya podés ingresar.';
                header('Location: ' . BASE_URL . '/login'); exit;
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
        session_regenerate_id(true); // Previene session fixation
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_rol']    = $user['rol'];
        $_SESSION['user_nombre'] = $user['nombre'] . ' ' . $user['apellido'];
        $_SESSION['user_cedula'] = $user['cedula'];
        $_SESSION['login_time']  = time();

        // Actualizar último login
        getDB()->prepare("UPDATE usuarios SET ultimo_login=NOW() WHERE id=:id")
               ->execute([':id' => $user['id']]);

        // Si es docente Y también tiene hijos en el sistema → selección de rol
        if ($user['rol'] === 'profesor' && $this->esTambienPadre($user['cedula'])) {
            $_SESSION['es_tambien_padre'] = true;
            header('Location: ' . BASE_URL . '/seleccionar-rol'); exit;
        }

        header('Location: ' . BASE_URL . '/dashboard'); exit;
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
