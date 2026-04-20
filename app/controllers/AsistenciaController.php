<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/NotaAusencia.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/AuthController.php';

class AsistenciaController {
    private Asistencia   $model;
    private Alumno       $alumnoModel;
    private Curso        $cursoModel;
    private Materia      $materiaModel;
    private NotaAusencia $notaModel;
    private Notificacion $notiModel;

    public function __construct() {
        $this->model        = new Asistencia();
        $this->alumnoModel  = new Alumno();
        $this->cursoModel   = new Curso();
        $this->materiaModel = new Materia();
        $this->notaModel    = new NotaAusencia();
        $this->notiModel    = new Notificacion();
    }

    public function index(): void {
        AuthController::check(['admin','directivo','profesor']);
        $cursos   = $this->cursoModel->getAll();
        $fecha    = $_GET['fecha']    ?? date('Y-m-d');
        $curso_id = $_GET['curso_id'] ?? null;
        $asistMap = [];
        $notasMap = [];
        $alumnos  = [];

        // Si es profesor, filtrar solo su materia
        $user_materia_id = null;
        if ($_SESSION['user_rol'] === 'profesor') {
            $db = getDB();
            $st = $db->prepare("SELECT materia_id FROM usuarios WHERE id=:id");
            $st->execute([':id' => $_SESSION['user_id']]);
            $user_materia_id = $st->fetchColumn() ?: null;
        }
        $materia_id = $_GET['materia_id'] ?? $user_materia_id;

        // Materias disponibles según rol
        if ($_SESSION['user_rol'] === 'profesor' && $user_materia_id) {
            $materias = [$this->materiaModel->findById($user_materia_id)];
        } else {
            $materias = $this->materiaModel->getAll();
        }

        if ($curso_id) {
            $alumnos   = $this->alumnoModel->getByCurso($curso_id);
            $registros = $this->model->getPorFechaYCurso($curso_id, $fecha, $materia_id);
            foreach ($registros as $r) {
                $asistMap[$r['alumno_id']] = $r;
            }
            // Cargar notas de ausencia existentes
            foreach ($asistMap as $r) {
                if ($r['id']) {
                    $nota = $this->notaModel->getByAsistencia($r['id']);
                    if ($nota) $notasMap[$r['id']] = $nota['nota'];
                }
            }
        }
        require __DIR__ . '/../views/asistencia/index.php';
    }

    public function guardar(): void {
        AuthController::check(['admin','directivo','profesor']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/asistencia'); exit;
        }
        $curso_id    = (int)$_POST['curso_id'];
        $materia_id  = !empty($_POST['materia_id']) ? (int)$_POST['materia_id'] : null;
        $fecha       = $_POST['fecha'];
        $profesor_id = (int)$_SESSION['user_id'];
        $notasAusencia = $_POST['nota_ausencia'] ?? [];

        foreach ($_POST['estados'] as $alumno_id => $estado) {
            $obs = $_POST['observaciones'][$alumno_id] ?? '';
            $this->model->registrar((int)$alumno_id, $curso_id, $materia_id, $fecha, $estado, $obs, $profesor_id);

            // Si hay nota de ausencia, guardarla y notificar al director
            if ($estado === 'ausente' && !empty($notasAusencia[$alumno_id])) {
                // Obtener el id de la asistencia recién guardada
                $asistId = $this->model->getIdPorAlumnoCursoFecha((int)$alumno_id, $curso_id, $fecha, $materia_id);
                if ($asistId) {
                    $this->notaModel->crear($asistId, $profesor_id, $notasAusencia[$alumno_id]);
                    // Notificar a directivos
                    $alumno = $this->alumnoModel->findById((int)$alumno_id);
                    foreach ($this->notiModel->getDirectivos() as $dirId) {
                        $this->notiModel->crear(
                            $dirId, 'nota_ausencia',
                            'Nota de ausencia — ' . $alumno['apellido'] . ', ' . $alumno['nombre'],
                            'Fecha: ' . date('d/m/Y', strtotime($fecha)) . ' — ' . $notasAusencia[$alumno_id],
                            $asistId
                        );
                    }
                }
            }
        }
        $_SESSION['success'] = 'Asistencia guardada correctamente.';
        header('Location: ' . BASE_URL . '/asistencia?curso_id=' . $curso_id . '&fecha=' . $fecha); exit;
    }

    public function reporteAlumno(int $id): void {
        AuthController::check(['admin','directivo','profesor']);
        $alumno    = $this->alumnoModel->findById($id);
        $desde     = $_GET['desde'] ?? date('Y-01-01');
        $hasta     = $_GET['hasta'] ?? date('Y-m-d');
        $registros = $this->model->getPorAlumno($id, $desde, $hasta);
        $resumen   = $this->model->getResumenAlumno($id);
        require __DIR__ . '/../views/asistencia/reporte_alumno.php';
    }

    public function reporteCurso(): void {
        AuthController::check(['admin','directivo','profesor']);
        $cursos   = $this->cursoModel->getAll();
        $curso_id = $_GET['curso_id'] ?? null;
        $mes      = $_GET['mes']  ?? date('m');
        $anio     = $_GET['anio'] ?? date('Y');
        $reporte  = [];
        $curso    = null;
        if ($curso_id) {
            $curso   = $this->cursoModel->findById((int)$curso_id);
            $reporte = $this->model->getResumenCurso((int)$curso_id, (int)$mes, (int)$anio);
        }
        require __DIR__ . '/../views/asistencia/reporte_curso.php';
    }

    public function exportarExcel(): void {
        AuthController::check(['admin','directivo']);
        $curso_id = $_GET['curso_id'] ?? null;
        $mes      = (int)($_GET['mes']  ?? date('m'));
        $anio     = (int)($_GET['anio'] ?? date('Y'));
        if (!$curso_id) { header('Location: ' . BASE_URL . '/reportes'); exit; }

        $curso   = $this->cursoModel->findById((int)$curso_id);
        $reporte = $this->model->getReporteMensual((int)$curso_id, $mes, $anio);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="asistencia_' . $curso['nombre'] . '_' . $mes . '_' . $anio . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, ['Fecha','Apellido','Nombre','Estado','Observaciones']);
        foreach ($reporte as $r) {
            fputcsv($out, [$r['fecha'], $r['apellido'], $r['nombre'], $r['estado'], $r['observaciones']]);
        }
        fclose($out);
        exit;
    }
}
