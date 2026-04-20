<?php
require_once __DIR__ . '/../../config/database.php';

class Materia {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function getAll(): array {
        return $this->db->query("SELECT * FROM materias ORDER BY nombre")->fetchAll();
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM materias WHERE id = :id");
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function create(array $data): int|false {
        $st = $this->db->prepare(
            "INSERT INTO materias (nombre, descripcion) VALUES (:n, :d) RETURNING id"
        );
        $st->execute([':n'=>$data['nombre'], ':d'=>$data['descripcion'] ?? null]);
        return $st->fetchColumn();
    }

    public function update(int $id, array $data): bool {
        $st = $this->db->prepare("UPDATE materias SET nombre=:n, descripcion=:d WHERE id=:id");
        return $st->execute([':n'=>$data['nombre'], ':d'=>$data['descripcion'] ?? null, ':id'=>$id]);
    }

    public function delete(int $id): bool {
        $st = $this->db->prepare("DELETE FROM materias WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }
}
