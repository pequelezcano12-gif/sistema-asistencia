<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/AuthController.php';

class UsuarioController {
    private Usuario $model;
    private Materia $materiaModel;

    public function __construct() {
        $this->model        = new Usuario();
        $this->materiaModel = new Materia();
    }

    public function index(): void {
        AuthController::check(['admin','directivo']);
        $usuarios = $this->model->getAll();
        require __DIR__ . '/../views/usuarios/index.php';
    }

    public function crear(): void {
        AuthController::check(['admin','directivo']);
        $materias = $this->materiaModel->getAll();
        $error    = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rol = $_POST['rol'];
            // Directivos solo pueden crear profesores
            if ($_SESSION['user_rol'] === 'directivo' && !in_array($rol, ['profesor'])) {
                $error = 'Solo podés crear cuentas de profesores.';
            } else {
                $data = [
                    'nombre'     => trim($_POST['nombre']),
                    'apellido'   => trim($_POST['apellido']),
                    'cedula'     => strtoupper(trim($_POST['cedula'])),
                    'email'      => strtolower(trim($_POST['email'])),
                    'password'   => $_POST['password'],
                    'rol'        => $rol,
                    'materia_id' => ($rol === 'profesor' && !empty($_POST['materia_id'])) ? (int)$_POST['materia_id'] : null,
                ];
                $id = $this->model->createConMateria($data);
                if ($id) {
                    header('Location: ' . BASE_URL . '/usuarios'); exit;
                }
                $error = 'Error al crear usuario. La cédula o email puede estar duplicado.';
            }
        }
        require __DIR__ . '/../views/usuarios/form.php';
    }

    public function editar(int $id): void {
        AuthController::check(['admin','directivo']);
        $usuario  = $this->model->findById($id);
        $materias = $this->materiaModel->getAll();
        if (!$usuario) { header('Location: ' . BASE_URL . '/usuarios'); exit; }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'     => trim($_POST['nombre']),
                'apellido'   => trim($_POST['apellido']),
                'email'      => strtolower(trim($_POST['email'])),
                'rol'        => $_POST['rol'],
                'activo'     => $_POST['activo'] ?? 1,
                'materia_id' => ($_POST['rol'] === 'profesor' && !empty($_POST['materia_id'])) ? (int)$_POST['materia_id'] : null,
            ];
            $this->model->updateConMateria($id, $data);
            if (!empty($_POST['password'])) {
                $this->model->updatePassword($id, $_POST['password']);
            }
            header('Location: ' . BASE_URL . '/usuarios'); exit;
        }
        require __DIR__ . '/../views/usuarios/form.php';
    }

    public function staff(): void {
        AuthController::check(['admin','directivo']);
        $materias = $this->materiaModel->getAll();
        require __DIR__ . '/../views/usuarios/form_staff.php';
    }

    public function registrarProfesor(): void {
        AuthController::check(['admin','directivo']);
        $materias = $this->materiaModel->getAll();
        $error    = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $this->procesarStaff('profesor');
            if (!$error) { header('Location: ' . BASE_URL . '/usuarios'); exit; }
        }
        require __DIR__ . '/../views/usuarios/form_profesor.php';
    }

    public function registrarDirectivo(): void {
        AuthController::check(['admin']);
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = $this->procesarStaff('directivo');
            if (!$error) { header('Location: ' . BASE_URL . '/usuarios'); exit; }
        }
        require __DIR__ . '/../views/usuarios/form_directivo.php';
    }

    public function crearStaff(): void {
        AuthController::check(['admin','directivo']);
        $tipo  = $_POST['tipo'] ?? 'profesor';
        $error = $this->procesarStaff($tipo);
        if (!$error) { header('Location: ' . BASE_URL . '/usuarios'); exit; }
        $_SESSION['error'] = $error;
        header('Location: ' . BASE_URL . '/usuarios/staff'); exit;
    }

    private function procesarStaff(string $tipo): ?string {
        $cedula   = strtoupper(trim($_POST['cedula'] ?? ''));
        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email    = strtolower(trim($_POST['email'] ?? ''));
        $materias = $this->materiaModel->getAll();

        if ($tipo === 'directivo' && $_SESSION['user_rol'] !== 'admin') {
            return 'Solo el administrador puede registrar directivos.';
        }
        if (empty($cedula) || empty($nombre) || empty($apellido)) {
            return 'Nombre, apellido y cédula son obligatorios.';
        }
        if ($this->model->findByCedula($cedula)) {
            return 'Ya existe un usuario con esa cédula.';
        }

        $data = [
            'nombre'     => $nombre,
            'apellido'   => $apellido,
            'cedula'     => $cedula,
            'email'      => $email ?: null,
            'rol'        => $tipo,
            'materia_id' => null,
        ];

        if ($tipo === 'profesor') {
            $materiaNombre = trim($_POST['materia_nombre'] ?? '');
            if (empty($materiaNombre)) return 'La asignatura es obligatoria.';
            $data['materia_id'] = $this->materiaModel->getOrCreate($materiaNombre);
            $data['password']         = bin2hex(random_bytes(16));
            $data['email_verificado'] = false;
        } else {
            $pass = $_POST['password'] ?? '';
            if (strlen($pass) < 8) return 'La contraseña debe tener al menos 8 caracteres.';
            $data['password']         = $pass;
            $data['email_verificado'] = true;
        }

        $id = $this->model->createConMateria($data);
        if (!$id) return 'Error al registrar. El email puede estar duplicado.';

        $_SESSION['success'] = ucfirst($tipo) . ' registrado correctamente.';
        return null;
    }

    public function eliminar(int $id): void {
        AuthController::check(['admin']);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/usuarios'); exit;
    }
}
