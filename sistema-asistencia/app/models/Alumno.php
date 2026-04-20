<?php
require_once __DIR__ . '/../../config/database.php';

class Alumno {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function getAll(?bool $activo = null): array {
        $where = $activo !== null ? "WHERE a.activo = " . ($activo ? 'TRUE' : 'FALSE') : '';
        $sql = "SELECT a.*, c.nombre AS curso_nombre
                FROM alumnos a
                LEFT JOIN inscripciones i ON a.id = i.alumno_id AND i.anio_lectivo = EXTRACT(YEAR FROM NOW())
                LEFT JOIN cursos c ON i.curso_id = c.id
                $where
                ORDER BY a.apellido, a.nombre";
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare(
            "SELECT a.*, c.nombre AS curso_nombre, c.id AS curso_id
             FROM alumnos a
             LEFT JOIN inscripciones i ON a.id = i.alumno_id AND i.anio_lectivo = EXTRACT(YEAR FROM NOW())
             LEFT JOIN cursos c ON i.curso_id = c.id
             WHERE a.id = :id"
        );
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function getByCurso(int $curso_id): array {
        $st = $this->db->prepare(
            "SELECT a.* FROM alumnos a
             JOIN inscripciones i ON a.id = i.alumno_id
             WHERE i.curso_id = :cid AND i.anio_lectivo = EXTRACT(YEAR FROM NOW()) AND a.activo = TRUE
             ORDER BY a.apellido, a.nombre"
        );
        $st->execute([':cid' => $curso_id]);
        return $st->fetchAll();
    }

    public function create(array $data): int|false {
        $st = $this->db->prepare(
            "INSERT INTO alumnos (nombre, apellido, dni, fecha_nacimiento, email, telefono, direccion, activo)
             VALUES (:n, :a, :d, :fn, :e, :t, :dir, TRUE) RETURNING id"
        );
        $st->execute([
            ':n'=>$data['nombre'], ':a'=>$data['apellido'], ':d'=>$data['dni'],
            ':fn'=>$data['fecha_nacimiento'] ?: null,
            ':e'=>$data['email'], ':t'=>$data['telefono'], ':dir'=>$data['direccion'],
        ]);
        return $st->fetchColumn();
    }

    public function update(int $id, array $data): bool {
        $st = $this->db->prepare(
            "UPDATE alumnos SET nombre=:n, apellido=:a, dni=:d, fecha_nacimiento=:fn,
             email=:e, telefono=:t, direccion=:dir, activo=:ac WHERE id=:id"
        );
        return $st->execute([
            ':n'=>$data['nombre'], ':a'=>$data['apellido'], ':d'=>$data['dni'],
            ':fn'=>$data['fecha_nacimiento'] ?: null,
            ':e'=>$data['email'], ':t'=>$data['telefono'], ':dir'=>$data['direccion'],
            ':ac'=>(bool)$data['activo'], ':id'=>$id,
        ]);
    }

    public function updateFoto(int $id, string $foto): bool {
        $st = $this->db->prepare("UPDATE alumnos SET foto=:f WHERE id=:id");
        return $st->execute([':f'=>$foto, ':id'=>$id]);
    }

    public function delete(int $id): bool {
        $st = $this->db->prepare("UPDATE alumnos SET activo=FALSE WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }

    public function search(string $q): array {
        $like = '%' . $q . '%';
        $st = $this->db->prepare(
            "SELECT * FROM alumnos
             WHERE (nombre ILIKE :q OR apellido ILIKE :q OR dni ILIKE :q) AND activo = TRUE"
        );
        $st->execute([':q' => $like]);
        return $st->fetchAll();
    }
}
