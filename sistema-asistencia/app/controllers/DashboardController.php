<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/AuthController.php';

class DashboardController {
    public function index(): void {
        AuthController::check();
        $db = getDB();

        $stats = [
            'alumnos'    => $db->query("SELECT COUNT(*) FROM alumnos WHERE activo=TRUE")->fetchColumn(),
            'cursos'     => $db->query("SELECT COUNT(*) FROM cursos WHERE activo=TRUE")->fetchColumn(),
            'profesores' => $db->query("SELECT COUNT(*) FROM usuarios WHERE rol='profesor' AND activo=TRUE")->fetchColumn(),
            'hoy'        => $db->query("SELECT COUNT(*) FROM asistencia WHERE fecha=CURRENT_DATE")->fetchColumn(),
        ];

        $ultimasAsistencias = $db->query(
            "SELECT a.fecha, al.nombre, al.apellido, a.estado, c.nombre AS curso
             FROM asistencia a
             JOIN alumnos al ON a.alumno_id = al.id
             JOIN cursos c ON a.curso_id = c.id
             ORDER BY a.created_at DESC LIMIT 10"
        )->fetchAll();

        $ausenciasMes = $db->query(
            "SELECT al.nombre, al.apellido, COUNT(*) AS total
             FROM asistencia a JOIN alumnos al ON a.alumno_id = al.id
             WHERE a.estado='ausente'
               AND EXTRACT(MONTH FROM a.fecha) = EXTRACT(MONTH FROM NOW())
               AND EXTRACT(YEAR  FROM a.fecha) = EXTRACT(YEAR  FROM NOW())
             GROUP BY al.id, al.nombre, al.apellido
             ORDER BY total DESC LIMIT 5"
        )->fetchAll();

        require __DIR__ . '/../views/dashboard.php';
    }
}
