<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/AuthController.php';

class CursoController {
    private $model;
    private $materiaModel;
    private $usuarioModel;

    public function __construct() {
        $this->model        = new Curso();
        $this->materiaModel = new Materia();
        $this->usuarioModel = new Usuario();
    }

    public function index() {
        AuthController::check(['admin','directivo','profesor']);
        $cursos = $this->model->getAll();
        require __DIR__ . '/../views/cursos/index.php';
    }

    public function crear() {
        AuthController::check(['admin','directivo']);
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'       => trim($_POST['nombre']),
                'turno'        => $_POST['turno'],
                'anio_lectivo' => $_POST['anio_lectivo'],
            ];
            if ($this->model->create($data)) {
                header('Location: ' . BASE_URL . '/cursos');
                exit;
            }
            $error = 'Error al crear curso.';
        }
        require __DIR__ . '/../views/cursos/form.php';
    }

    public function editar($id) {
        AuthController::check(['admin','directivo']);
        $curso = $this->model->findById($id);
        if (!$curso) { header('Location: ' . BASE_URL . '/cursos'); exit; }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'       => trim($_POST['nombre']),
                'turno'        => $_POST['turno'],
                'anio_lectivo' => $_POST['anio_lectivo'],
                'activo'       => $_POST['activo'] ?? 1,
            ];
            if ($this->model->update($id, $data)) {
                header('Location: ' . BASE_URL . '/cursos');
                exit;
            }
            $error = 'Error al actualizar.';
        }
        require __DIR__ . '/../views/cursos/form.php';
    }

    public function eliminar($id) {
        AuthController::check(['admin','directivo']);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/cursos');
        exit;
    }

    public function materias($id) {
        AuthController::check(['admin','directivo']);
        $curso    = $this->model->findById($id);
        $materias = $this->materiaModel->getAll();
        $asignadas = $this->model->getMaterias($id);
        $profesores = $this->usuarioModel->getAll('profesor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $materia_id  = $_POST['materia_id'];
            $profesor_id = $_POST['profesor_id'] ?: null;
            $this->model->asignarMateria($id, $materia_id, $profesor_id);
            header('Location: ' . BASE_URL . '/cursos/' . $id . '/materias');
            exit;
        }
        require __DIR__ . '/../views/cursos/materias.php';
    }
}
