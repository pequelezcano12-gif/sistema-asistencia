<?php
require_once __DIR__ . '/../../config/database.php';

class Justificativo {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function crear(array $data): int|false {
        $st = $this->db->prepare(
            "INSERT INTO justificativos (alumno_id, asistencia_id, fecha_ausencia, motivo, archivo, padre_usuario_id)
             VALUES (:al, :asi, :fe, :mo, :ar, :pa) RETURNING id"
        );
        $st->execute([
            ':al'=>$data['alumno_id'], ':asi'=>$data['asistencia_id'] ?? null,
            ':fe'=>$data['fecha_ausencia'], ':mo'=>$data['motivo'],
            ':ar'=>$data['archivo'] ?? null, ':pa'=>$data['padre_usuario_id'],
        ]);
        return $st->fetchColumn();
    }

    public function getPendientes(): array {
        return $this->db->query(
            "SELECT j.*, al.nombre, al.apellido, al.dni,
                    u.nombre AS padre_nombre, u.apellido AS padre_apellido
             FROM justificativos j
             JOIN alumnos al ON j.alumno_id = al.id
             LEFT JOIN usuarios u ON j.padre_usuario_id = u.id
             WHERE j.estado = 'pendiente'
             ORDER BY j.created_at DESC"
        )->fetchAll();
    }

    public function getTodos(): array {
        return $this->db->query(
            "SELECT j.*, al.nombre, al.apellido,
                    u.nombre AS padre_nombre, u.apellido AS padre_apellido
             FROM justificativos j
             JOIN alumnos al ON j.alumno_id = al.id
             LEFT JOIN usuarios u ON j.padre_usuario_id = u.id
             ORDER BY j.created_at DESC LIMIT 100"
        )->fetchAll();
    }

    public function getByAlumno(int $alumno_id): array {
        $st = $this->db->prepare(
            "SELECT * FROM justificativos WHERE alumno_id=:al ORDER BY created_at DESC"
        );
        $st->execute([':al'=>$alumno_id]);
        return $st->fetchAll();
    }

    public function aprobar(int $id): void {
        $st = $this->db->prepare(
            "UPDATE justificativos SET estado='aprobado', visto_director=TRUE WHERE id=:id"
        );
        $st->execute([':id'=>$id]);
        // Actualizar asistencia a justificado
        $j = $this->findById($id);
        if ($j && $j['asistencia_id']) {
            $this->db->prepare("UPDATE asistencia SET estado='justificado' WHERE id=:id")
                     ->execute([':id'=>$j['asistencia_id']]);
        }
    }

    public function rechazar(int $id): void {
        $st = $this->db->prepare(
            "UPDATE justificativos SET estado='rechazado', visto_director=TRUE WHERE id=:id"
        );
        $st->execute([':id'=>$id]);
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM justificativos WHERE id=:id");
        $st->execute([':id'=>$id]);
        return $st->fetch() ?: null;
    }
}
