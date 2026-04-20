<?php
require_once __DIR__ . '/../../config/database.php';

class NotaAusencia {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function crear(int $asistencia_id, int $profesor_id, string $nota): int|false {
        $st = $this->db->prepare(
            "INSERT INTO notas_ausencia (asistencia_id, profesor_id, nota)
             VALUES (:a, :p, :n)
             ON CONFLICT DO NOTHING RETURNING id"
        );
        $st->execute([':a'=>$asistencia_id, ':p'=>$profesor_id, ':n'=>$nota]);
        return $st->fetchColumn();
    }

    public function getByAsistencia(int $asistencia_id): ?array {
        $st = $this->db->prepare(
            "SELECT na.*, u.nombre AS prof_nombre, u.apellido AS prof_apellido
             FROM notas_ausencia na
             JOIN usuarios u ON na.profesor_id = u.id
             WHERE na.asistencia_id = :a"
        );
        $st->execute([':a'=>$asistencia_id]);
        return $st->fetch() ?: null;
    }

    public function getNoVistas(): array {
        return $this->db->query(
            "SELECT na.*, al.nombre, al.apellido, a.fecha, c.nombre AS curso,
                    u.nombre AS prof_nombre, u.apellido AS prof_apellido
             FROM notas_ausencia na
             JOIN asistencia a ON na.asistencia_id = a.id
             JOIN alumnos al ON a.alumno_id = al.id
             JOIN cursos c ON a.curso_id = c.id
             JOIN usuarios u ON na.profesor_id = u.id
             WHERE na.visto_director = FALSE
             ORDER BY na.created_at DESC"
        )->fetchAll();
    }

    public function marcarVista(int $id): void {
        $this->db->prepare("UPDATE notas_ausencia SET visto_director=TRUE WHERE id=:id")
                 ->execute([':id'=>$id]);
    }
}
