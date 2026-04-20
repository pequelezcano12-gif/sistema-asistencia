<?php
require_once __DIR__ . '/../../config/database.php';

class Inscripcion {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function inscribir(int $alumno_id, int $curso_id, int $anio_lectivo): bool {
        $st = $this->db->prepare(
            "INSERT INTO inscripciones (alumno_id, curso_id, anio_lectivo)
             VALUES (:al, :cu, :an)
             ON CONFLICT (alumno_id, curso_id, anio_lectivo) DO NOTHING"
        );
        return $st->execute([':al'=>$alumno_id, ':cu'=>$curso_id, ':an'=>$anio_lectivo]);
    }

    public function desinscribir(int $alumno_id, int $curso_id): bool {
        $st = $this->db->prepare(
            "DELETE FROM inscripciones WHERE alumno_id=:al AND curso_id=:cu"
        );
        return $st->execute([':al'=>$alumno_id, ':cu'=>$curso_id]);
    }

    public function getByAlumno(int $alumno_id): array {
        $st = $this->db->prepare(
            "SELECT i.*, c.nombre AS curso_nombre, c.turno
             FROM inscripciones i
             JOIN cursos c ON i.curso_id = c.id
             WHERE i.alumno_id = :al ORDER BY i.anio_lectivo DESC"
        );
        $st->execute([':al' => $alumno_id]);
        return $st->fetchAll();
    }
}
