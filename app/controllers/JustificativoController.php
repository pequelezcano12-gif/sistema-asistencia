<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Justificativo.php';
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Asistencia.php';
require_once __DIR__ . '/../models/Notificacion.php';
require_once __DIR__ . '/AuthController.php';

class JustificativoController {
    private Justificativo $model;
    private Notificacion  $notiModel;
    private Alumno        $alumnoModel;

    public function __construct() {
        $this->model       = new Justificativo();
        $this->notiModel   = new Notificacion();
        $this->alumnoModel = new Alumno();
    }

    public function index(): void {
        AuthController::check(['admin','directivo']);
        $justificativos = $this->model->getTodos();
        require __DIR__ . '/../views/justificativos/index.php';
    }

    public function crear(): void {
        // Solo padres, admin y directivos pueden enviar justificativos
        AuthController::check(['admin','directivo','padre']);
        if ($_SESSION['user_rol'] === 'alumno') {
            $_SESSION['error'] = 'Solo el padre, madre o encargado puede enviar justificativos.';
            header('Location: ' . BASE_URL . '/alumnos/perfil'); exit;
        }
        $error     = null;
        $alumno_id = $_GET['alumno_id'] ?? null;

        // Obtener alumnos del padre/usuario actual
        $misAlumnos = $this->getMisAlumnos();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $alumno_id     = (int)$_POST['alumno_id'];
            $fecha_ausencia= $_POST['fecha_ausencia'];
            $motivo        = trim($_POST['motivo']);

            // Verificar que el alumno pertenece al padre
            $ids = array_column($misAlumnos, 'id');
            if (!in_array($alumno_id, $ids)) {
                $error = 'No tenés permiso para justificar a ese alumno.';
            } elseif (empty($motivo)) {
                $error = 'El motivo es obligatorio.';
            } else {
                // Buscar asistencia del día
                $asistModel = new Asistencia();
                $registros  = $asistModel->getPorAlumno($alumno_id, $fecha_ausencia, $fecha_ausencia);
                $asistId    = !empty($registros) ? $registros[0]['id'] : null;

                // Subir archivo si hay
                $archivo = null;
                if (!empty($_FILES['archivo']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['pdf','jpg','jpeg','png'])) {
                        $dir = __DIR__ . '/../../storage/uploads/justificativos/';
                        if (!is_dir($dir)) mkdir($dir, 0755, true);
                        $archivo = 'just_' . time() . '_' . $alumno_id . '.' . $ext;
                        move_uploaded_file($_FILES['archivo']['tmp_name'], $dir . $archivo);
                    }
                }

                $id = $this->model->crear([
                    'alumno_id'       => $alumno_id,
                    'asistencia_id'   => $asistId,
                    'fecha_ausencia'  => $fecha_ausencia,
                    'motivo'          => $motivo,
                    'archivo'         => $archivo,
                    'padre_usuario_id'=> $_SESSION['user_id'],
                ]);

                if ($id) {
                    // Notificar a directivos
                    $alumno = $this->alumnoModel->findById($alumno_id);
                    foreach ($this->notiModel->getDirectivos() as $dirId) {
                        $this->notiModel->crear(
                            $dirId, 'justificativo',
                            'Justificativo — ' . $alumno['apellido'] . ', ' . $alumno['nombre'],
                            'Fecha: ' . date('d/m/Y', strtotime($fecha_ausencia)) . ' — ' . substr($motivo, 0, 100),
                            $id
                        );
                    }
                    $_SESSION['success'] = 'Justificativo enviado al director correctamente.';
                    header('Location: ' . BASE_URL . '/dashboard'); exit;
                }
                $error = 'Error al enviar el justificativo.';
            }
        }
        require __DIR__ . '/../views/justificativos/crear.php';
    }

    public function aprobar(int $id): void {
        AuthController::check(['admin','directivo']);
        $this->model->aprobar($id);
        $j = $this->model->findById($id);
        // Notificar al padre
        if ($j && $j['padre_usuario_id']) {
            $alumno = $this->alumnoModel->findById($j['alumno_id']);
            $this->notiModel->crear(
                $j['padre_usuario_id'], 'justificativo_aprobado',
                'Justificativo aprobado — ' . $alumno['apellido'] . ', ' . $alumno['nombre'],
                'El justificativo del ' . date('d/m/Y', strtotime($j['fecha_ausencia'])) . ' fue aprobado.',
                $id
            );
        }
        $_SESSION['success'] = 'Justificativo aprobado. La asistencia fue actualizada.';
        header('Location: ' . BASE_URL . '/justificativos'); exit;
    }

    public function rechazar(int $id): void {
        AuthController::check(['admin','directivo']);
        $this->model->rechazar($id);
        $j = $this->model->findById($id);
        if ($j && $j['padre_usuario_id']) {
            $alumno = $this->alumnoModel->findById($j['alumno_id']);
            $this->notiModel->crear(
                $j['padre_usuario_id'], 'justificativo_rechazado',
                'Justificativo rechazado — ' . $alumno['apellido'] . ', ' . $alumno['nombre'],
                'El justificativo del ' . date('d/m/Y', strtotime($j['fecha_ausencia'])) . ' fue rechazado.',
                $id
            );
        }
        $_SESSION['success'] = 'Justificativo rechazado.';
        header('Location: ' . BASE_URL . '/justificativos'); exit;
    }

    private function getMisAlumnos(): array {
        $db = getDB();
        $userId = $_SESSION['user_id'];
        // Buscar alumnos donde el usuario es padre/madre
        $st = $db->prepare(
            "SELECT a.* FROM alumnos a
             JOIN usuarios u ON (a.cedula_padre = u.cedula OR a.cedula_madre = u.cedula)
             WHERE u.id = :uid AND a.activo = TRUE"
        );
        $st->execute([':uid' => $userId]);
        $alumnos = $st->fetchAll();
        // Si es admin/directivo, puede ver todos
        if (empty($alumnos) && in_array($_SESSION['user_rol'], ['admin','directivo'])) {
            return $this->alumnoModel->getAll(true);
        }
        return $alumnos;
    }
}
