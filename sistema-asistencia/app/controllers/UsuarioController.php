<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/AuthController.php';

class UsuarioController {
    private $model;

    public function __construct() { $this->model = new Usuario(); }

    public function index() {
        AuthController::check(['admin','directivo']);
        $usuarios = $this->model->getAll();
        require __DIR__ . '/../views/usuarios/index.php';
    }

    public function crear() {
        AuthController::check(['admin']);
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'   => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'email'    => trim($_POST['email']),
                'password' => $_POST['password'],
                'rol'      => $_POST['rol'],
            ];
            if ($this->model->create($data)) {
                header('Location: ' . BASE_URL . '/usuarios');
                exit;
            }
            $error = 'Error al crear usuario. El email puede estar duplicado.';
        }
        require __DIR__ . '/../views/usuarios/form.php';
    }

    public function editar($id) {
        AuthController::check(['admin']);
        $usuario = $this->model->findById($id);
        if (!$usuario) { header('Location: ' . BASE_URL . '/usuarios'); exit; }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'   => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'email'    => trim($_POST['email']),
                'rol'      => $_POST['rol'],
                'activo'   => $_POST['activo'] ?? 1,
            ];
            $this->model->update($id, $data);
            if (!empty($_POST['password'])) {
                $this->model->updatePassword($id, $_POST['password']);
            }
            header('Location: ' . BASE_URL . '/usuarios');
            exit;
        }
        require __DIR__ . '/../views/usuarios/form.php';
    }

    public function eliminar($id) {
        AuthController::check(['admin']);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/usuarios');
        exit;
    }
}
