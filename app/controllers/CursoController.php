<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/AuthController.php';

class CursoController {
    private Curso   $model;
    private Materia $materiaModel;
    private Usuario $usuarioModel;

    public function __construct() {
        $this->model        = new Curso();
        $this->materiaModel = new Materia();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void {
        AuthController::check(['admin','directivo','profesor']);
        $cursos = $this->model->getAll();
        require __DIR__ . '/../views/cursos/index.php';
    }

    public function crear(): void {
        AuthController::check(['admin','directivo']);
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $this->construirNombre();
            $turno  = $_POST['turno'];
            $nivel  = $_POST['nivel'];
            // Básica superior solo mañana
            if ($nivel === 'basica_superior') $turno = 'manana';

            $data = [
                'nombre'       => $nombre,
                'turno'        => $turno,
                'anio_lectivo' => $_POST['anio_lectivo'],
                'nivel'        => $nivel,
                'especialidad' => $_POST['especialidad'] ?? 'ninguna',
            ];
            if ($this->model->create($data)) {
                header('Location: ' . BASE_URL . '/cursos'); exit;
            }
            $error = 'Error al crear el curso. Puede que ya exista uno con ese nombre.';
        }
        require __DIR__ . '/../views/cursos/form.php';
    }

    public function editar(int $id): void {
        AuthController::check(['admin','directivo']);
        $curso = $this->model->findById($id);
        if (!$curso) { header('Location: ' . BASE_URL . '/cursos'); exit; }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $this->construirNombre();
            $turno  = $_POST['turno'];
            $nivel  = $_POST['nivel'];
            if ($nivel === 'basica_superior') $turno = 'manana';

            $data = [
                'nombre'       => $nombre,
                'turno'        => $turno,
                'anio_lectivo' => $_POST['anio_lectivo'],
                'nivel'        => $nivel,
                'especialidad' => $_POST['especialidad'] ?? 'ninguna',
                'activo'       => $_POST['activo'] ?? 1,
            ];
            if ($this->model->update($id, $data)) {
                header('Location: ' . BASE_URL . '/cursos'); exit;
            }
            $error = 'Error al actualizar.';
        }
        require __DIR__ . '/../views/cursos/form.php';
    }

    public function eliminar(int $id): void {
        AuthController::check(['admin','directivo']);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/cursos'); exit;
    }

    public function materias(int $id): void {
        AuthController::check(['admin','directivo']);
        $curso      = $this->model->findById($id);
        $materias   = $this->materiaModel->getAll();
        $asignadas  = $this->model->getMaterias($id);
        $profesores = $this->usuarioModel->getAll('profesor');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->asignarMateria($id, (int)$_POST['materia_id'], $_POST['profesor_id'] ?: null);
            header('Location: ' . BASE_URL . '/cursos/' . $id . '/materias'); exit;
        }
        require __DIR__ . '/../views/cursos/materias.php';
    }

    private function construirNombre(): string {
        // Si viene el nombre generado por JS, usarlo
        $nombre = trim($_POST['nombre'] ?? '');
        if (!empty($nombre)) return $nombre;
        // Fallback: construir desde grado + paralelo
        $grado   = trim($_POST['grado']   ?? '');
        $paralelo= trim($_POST['paralelo'] ?? '');
        return $grado && $paralelo ? $grado . '-' . $paralelo : 'Sin nombre';
    }
}
