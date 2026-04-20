<?php
require_once __DIR__ . '/../../config/database.php';

class Curso {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function getAll(): array {
        return $this->db->query(
            "SELECT c.*, COUNT(DISTINCT i.alumno_id) AS total_alumnos
             FROM cursos c
             LEFT JOIN inscripciones i ON c.id = i.curso_id AND i.anio_lectivo = c.anio_lectivo
             WHERE c.activo = TRUE
             GROUP BY c.id ORDER BY c.anio_lectivo DESC, c.nombre"
        )->fetchAll();
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM cursos WHERE id = :id");
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function create(array $data): int|false {
        $st = $this->db->prepare(
            "INSERT INTO cursos (nombre, turno, anio_lectivo) VALUES (:n, :t, :a) RETURNING id"
        );
        $st->execute([':n'=>$data['nombre'], ':t'=>$data['turno'], ':a'=>$data['anio_lectivo']]);
        return $st->fetchColumn();
    }

    public function update(int $id, array $data): bool {
        $st = $this->db->prepare(
            "UPDATE cursos SET nombre=:n, turno=:t, anio_lectivo=:a, activo=:ac WHERE id=:id"
        );
        return $st->execute([':n'=>$data['nombre'],':t'=>$data['turno'],':a'=>$data['anio_lectivo'],
                             ':ac'=>(bool)$data['activo'],':id'=>$id]);
    }

    public function delete(int $id): bool {
        $st = $this->db->prepare("UPDATE cursos SET activo=FALSE WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }

    public function getMaterias(int $curso_id): array {
        $st = $this->db->prepare(
            "SELECT m.*, u.nombre AS prof_nombre, u.apellido AS prof_apellido
             FROM curso_materia cm
             JOIN materias m ON cm.materia_id = m.id
             LEFT JOIN usuarios u ON cm.profesor_id = u.id
             WHERE cm.curso_id = :cid"
        );
        $st->execute([':cid' => $curso_id]);
        return $st->fetchAll();
    }

    public function asignarMateria(int $curso_id, int $materia_id, ?int $profesor_id): bool {
        $st = $this->db->prepare(
            "INSERT INTO curso_materia (curso_id, materia_id, profesor_id) VALUES (:c, :m, :p)
             ON CONFLICT (curso_id, materia_id) DO UPDATE SET profesor_id = EXCLUDED.profesor_id"
        );
        return $st->execute([':c'=>$curso_id, ':m'=>$materia_id, ':p'=>$profesor_id]);
    }
}
