<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Justificativo.php';
require_once __DIR__ . '/AuthController.php';

class AlumnoController {
    private Alumno      $model;
    private Curso       $cursoModel;
    private Inscripcion $inscModel;

    public function __construct() {
        $this->model      = new Alumno();
        $this->cursoModel = new Curso();
        $this->inscModel  = new Inscripcion();
    }

    public function index(): void {
        AuthController::check(['admin','directivo','profesor']);
        $alumnos = $this->model->getAll();
        $cursos  = $this->cursoModel->getAll();
        require __DIR__ . '/../views/alumnos/index.php';
    }

    public function crear(): void {
        AuthController::check(['admin','directivo']);
        $cursos = $this->cursoModel->getAll();
        $error  = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->datosDesdePost();
            $id   = $this->model->create($data);
            if ($id) {
                if (!empty($_POST['curso_id'])) {
                    $this->inscModel->inscribir($id, (int)$_POST['curso_id'], (int)date('Y'));
                }
                header('Location: ' . BASE_URL . '/alumnos'); exit;
            }
            $error = 'Error al crear alumno. El DNI puede estar duplicado.';
        }
        require __DIR__ . '/../views/alumnos/form.php';
    }

    public function editar(int $id): void {
        AuthController::check(['admin','directivo']);
        $alumno = $this->model->findById($id);
        $cursos = $this->cursoModel->getAll();
        $error  = null;
        if (!$alumno) { header('Location: ' . BASE_URL . '/alumnos'); exit; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->datosDesdePost(true);
            if ($this->model->update($id, $data)) {
                if (!empty($_POST['curso_id'])) {
                    $this->inscModel->inscribir($id, (int)$_POST['curso_id'], (int)date('Y'));
                }
                header('Location: ' . BASE_URL . '/alumnos'); exit;
            }
            $error = 'Error al actualizar.';
        }
        require __DIR__ . '/../views/alumnos/form.php';
    }

    public function eliminar(int $id): void {
        AuthController::check(['admin','directivo']);
        $this->model->delete($id);
        header('Location: ' . BASE_URL . '/alumnos'); exit;
    }

    public function ver(int $id): void {
        AuthController::check(['admin','directivo','profesor']);
        $alumno = $this->model->findById($id);
        if (!$alumno) { header('Location: ' . BASE_URL . '/alumnos'); exit; }
        $inscripciones = $this->inscModel->getByAlumno($id);
        require __DIR__ . '/../views/alumnos/ver.php';
    }

    public function perfil(): void {
        AuthController::check(['alumno']);
        // Buscar el alumno por la cédula del usuario logueado
        $db = getDB();
        $st = $db->prepare("SELECT id FROM alumnos WHERE dni = :dni AND activo = TRUE");
        $st->execute([':dni' => $_SESSION['user_cedula']]);
        $alumnoId = $st->fetchColumn();

        if (!$alumnoId) {
            $_SESSION['error'] = 'No se encontró tu registro de alumno.';
            header('Location: ' . BASE_URL . '/dashboard'); exit;
        }

        $asistenciaModel  = new \Asistencia();
        $justificativoModel = new \Justificativo();

        $alumno        = $this->model->findById($alumnoId);
        $registros     = $asistenciaModel->getPorAlumno($alumnoId, date('Y-01-01'), date('Y-m-d'));
        $resumen       = $asistenciaModel->getResumenAlumno($alumnoId);
        $justificativos= $justificativoModel->getByAlumno($alumnoId);

        require __DIR__ . '/../views/alumnos/perfil.php';
    }

    private function datosDesdePost(bool $conActivo = false): array {
        $data = [
            'nombre'             => trim($_POST['nombre']),
            'apellido'           => trim($_POST['apellido']),
            'dni'                => trim($_POST['dni']),
            'fecha_nacimiento'   => $_POST['fecha_nacimiento'] ?: null,
            'email'              => trim($_POST['email'] ?? ''),
            'telefono'           => trim($_POST['telefono'] ?? ''),
            'direccion'          => trim($_POST['direccion'] ?? ''),
            'cedula_padre'       => trim($_POST['cedula_padre'] ?? ''),
            'nombre_encargado'   => trim($_POST['nombre_encargado'] ?? ''),
            'telefono_encargado' => trim($_POST['telefono_encargado'] ?? ''),
        ];
        if ($conActivo) $data['activo'] = $_POST['activo'] ?? 1;
        return $data;
    }
}
