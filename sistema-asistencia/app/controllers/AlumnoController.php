<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/AuthController.php';

class AlumnoController {
    private $model;
    private $cursoModel;
    private $inscModel;

    public function __construct() {
        $this->model    = new Alumno();
        $this->cursoModel = new Curso();
        $this->inscModel  = new Inscripcion();
    }

    public function index() {
        AuthController::check(['admin','directivo','profesor']);
        $alumnos = $this->model->getAll();
        $cursos  = $this->cursoModel->getAll();
        require __DIR__ . '/../views/alumnos/index.php';
    }

    public function crear() {
        AuthController::check(['admin','directivo']);
        $cursos = $this->cursoModel->getAll();
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'          => trim($_POST['nombre']),
                'apellido'        => trim($_POST['apellido']),
                'dni'             => trim($_POST['dni']),
                'fecha_nacimiento'=> $_POST['fecha_nacimiento'],
                'email'           => trim($_POST['email']),
                'telefono'        => trim($_POST['telefono']),
                'direccion'       => trim($_POST['direccion']),
            ];
            $id = $this->model->create($data);
            if ($id) {
                if (!empty($_POST['curso_id'])) {
                    $this->inscModel->inscribir($id, $_POST['curso_id'], date('Y'));
                }
                $this->subirFoto($id);
                header('Location: ' . BASE_URL . '/alumnos');
                exit;
            }
            $error = 'Error al crear alumno. El DNI puede estar duplicado.';
        }
        require __DIR__ . '/../views/alumnos/form.php';
    }

    public function editar($id) {
        AuthController::check(['admin','directivo']);
        $alumno = $this->model->findById($id);
        $cursos = $this->cursoModel->getAll();
        $error  = null;
        if (!$alumno) { header('Location: ' . BASE_URL . '/alumnos'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nombre'          => trim($_POST['nombre']),
                'apellido'        => trim($_POST['apellido']),
                'dni'             => trim($_POST['dni']),
                'fecha_nacimiento'=> $_POST['fecha_nacimiento'],
                'email'           => trim($_POST['email']),
                'telefono'        => trim($_POST['telefono']),
                'direccion'       => trim($_POST['direccion']),
                'activo'          => $_POST['activo'] ?? 1,
            ];
            if ($this->model->update($id, $data)) {
                if (!empty($_POST['curso_id'])) {
                    $this->inscModel->inscribir($id, $_POST['curso_id'], date('Y'));
                }
                $this->subirFoto($id);
                header('Location: ' . BASE_URL . '/alumnos');
                exit;
            }
            $error = 'Error al actualizar.';
        }
        require __DIR__ . '/../views/alumnos/form.php';
    }

    public function eliminar($id) {
        AuthController::check(['admin','directivo']);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/alumnos');
        exit;
    }

    public function ver($id) {
        AuthController::check(['admin','directivo','profesor']);
        $alumno = $this->model->findById($id);
        if (!$alumno) { header('Location: ' . BASE_URL . '/alumnos'); exit; }
        $inscripciones = $this->inscModel->getByAlumno($id);
        require __DIR__ . '/../views/alumnos/ver.php';
    }

    private function subirFoto($id) {
        if (!empty($_FILES['foto']['name'])) {
            $ext  = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];
            if (in_array($ext, $allowed)) {
                $dir  = __DIR__ . '/../../storage/uploads/alumnos/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $name = "alumno_{$id}." . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $name);
                $this->model->updateFoto($id, $name);
            }
        }
    }
}
