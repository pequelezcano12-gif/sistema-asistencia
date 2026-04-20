<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/AuthController.php';

class AsistenciaController {
    private $model;
    private $alumnoModel;
    private $cursoModel;
    private $materiaModel;

    public function __construct() {
        $this->model        = new Asistencia();
        $this->alumnoModel  = new Alumno();
        $this->cursoModel   = new Curso();
        $this->materiaModel = new Materia();
    }

    public function index() {
        AuthController::check(['admin','directivo','profesor']);
        $cursos  = $this->cursoModel->getAll();
        $materias = $this->materiaModel->getAll();
        $fecha   = $_GET['fecha']   ?? date('Y-m-d');
        $curso_id = $_GET['curso_id'] ?? null;
        $materia_id = $_GET['materia_id'] ?? null;
        $registros = [];
        $alumnos   = [];

        if ($curso_id) {
            $alumnos   = $this->alumnoModel->getByCurso($curso_id);
            $registros = $this->model->getPorFechaYCurso($curso_id, $fecha, $materia_id);
            // Indexar por alumno_id para fácil acceso en la vista
            $asistMap = [];
            foreach ($registros as $r) $asistMap[$r['alumno_id']] = $r;
        }
        require __DIR__ . '/../views/asistencia/index.php';
    }

    public function guardar() {
        AuthController::check(['admin','directivo','profesor']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/asistencia');
            exit;
        }
        $curso_id   = $_POST['curso_id'];
        $materia_id = $_POST['materia_id'] ?: null;
        $fecha      = $_POST['fecha'];
        $profesor_id = $_SESSION['user_id'];

        foreach ($_POST['estados'] as $alumno_id => $estado) {
            $obs = $_POST['observaciones'][$alumno_id] ?? '';
            $this->model->registrar($alumno_id, $curso_id, $materia_id, $fecha, $estado, $obs, $profesor_id);
        }
        $_SESSION['success'] = 'Asistencia guardada correctamente.';
        header('Location: ' . BASE_URL . '/asistencia?curso_id=' . $curso_id . '&fecha=' . $fecha);
        exit;
    }

    public function reporteAlumno($id) {
        AuthController::check(['admin','directivo','profesor']);
        $alumno   = $this->alumnoModel->findById($id);
        $desde    = $_GET['desde'] ?? date('Y-01-01');
        $hasta    = $_GET['hasta'] ?? date('Y-m-d');
        $registros = $this->model->getPorAlumno($id, $desde, $hasta);
        $resumen   = $this->model->getResumenAlumno($id);
        require __DIR__ . '/../views/asistencia/reporte_alumno.php';
    }

    public function reporteCurso() {
        AuthController::check(['admin','directivo','profesor']);
        $cursos  = $this->cursoModel->getAll();
        $curso_id = $_GET['curso_id'] ?? null;
        $mes     = $_GET['mes']  ?? date('m');
        $anio    = $_GET['anio'] ?? date('Y');
        $reporte = [];
        $curso   = null;
        if ($curso_id) {
            $curso   = $this->cursoModel->findById($curso_id);
            $reporte = $this->model->getResumenCurso($curso_id, $mes, $anio);
        }
        require __DIR__ . '/../views/asistencia/reporte_curso.php';
    }

    public function exportarExcel() {
        AuthController::check(['admin','directivo']);
        $curso_id = $_GET['curso_id'] ?? null;
        $mes      = $_GET['mes']  ?? date('m');
        $anio     = $_GET['anio'] ?? date('Y');
        if (!$curso_id) { header('Location: ' . BASE_URL . '/reportes'); exit; }

        $curso   = $this->cursoModel->findById($curso_id);
        $reporte = $this->model->getReporteMensual($curso_id, $mes, $anio);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="asistencia_' . $curso['nombre'] . '_' . $mes . '_' . $anio . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        fputcsv($out, ['Fecha','Apellido','Nombre','Estado','Observaciones']);
        foreach ($reporte as $r) {
            fputcsv($out, [$r['fecha'], $r['apellido'], $r['nombre'], $r['estado'], $r['observaciones']]);
        }
        fclose($out);
        exit;
    }
}
