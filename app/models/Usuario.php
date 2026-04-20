<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function findByCedula(string $cedula): ?array {
        $st = $this->db->prepare("SELECT * FROM usuarios WHERE cedula = :c AND activo = TRUE");
        $st->execute([':c' => $cedula]);
        return $st->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $st = $this->db->prepare("SELECT * FROM usuarios WHERE email = :e AND activo = TRUE");
        $st->execute([':e' => $email]);
        return $st->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $st->execute([':id' => $id]);
        return $st->fetch() ?: null;
    }

    public function getAlumnoByCedula(string $cedula): ?array {
        $st = $this->db->prepare("SELECT * FROM alumnos WHERE dni = :c AND activo = TRUE");
        $st->execute([':c' => $cedula]);
        return $st->fetch() ?: null;
    }

    public function getAll(?string $rol = null): array {
        if ($rol) {
            $st = $this->db->prepare("SELECT * FROM usuarios WHERE rol = :r AND activo = TRUE ORDER BY apellido, nombre");
            $st->execute([':r' => $rol]);
        } else {
            $st = $this->db->query("SELECT * FROM usuarios ORDER BY rol, apellido, nombre");
        }
        return $st->fetchAll();
    }

    public function create(array $data): int|false {
        $hash = password_hash($data['password'], PASSWORD_ARGON2ID);
        $st = $this->db->prepare(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol, cedula, email_verificado)
             VALUES (:n, :a, :e, :p, :r, :c, FALSE) RETURNING id"
        );
        $st->execute([
            ':n'=>$data['nombre'], ':a'=>$data['apellido'], ':e'=>$data['email'],
            ':p'=>$hash, ':r'=>$data['rol'], ':c'=>$data['cedula'] ?? null,
        ]);
        return $st->fetchColumn();
    }

    public function createFromRegistro(array $data): int|false {
        $hash = password_hash($data['password'], PASSWORD_ARGON2ID);
        $nombre   = $data['nombre']   ?? '';
        $apellido = $data['apellido'] ?? '';

        // Si es alumno, tomar nombre del registro de alumnos
        if ($data['rol'] === 'alumno') {
            $alumno   = $this->getAlumnoByCedula($data['cedula']);
            $nombre   = $alumno ? $alumno['nombre']   : $nombre;
            $apellido = $alumno ? $alumno['apellido']  : $apellido;
        }

        // Si es profesor pre-registrado, actualizar su registro existente
        if ($data['rol'] === 'profesor') {
            $st = $this->db->prepare("SELECT id FROM usuarios WHERE cedula=:c AND rol='profesor'");
            $st->execute([':c' => $data['cedula']]);
            $existente = $st->fetch();
            if ($existente) {
                $st = $this->db->prepare(
                    "UPDATE usuarios SET password=:p, email=:e, nombre=:n, apellido=:a,
                     email_verificado=FALSE WHERE id=:id RETURNING id"
                );
                $st->execute([':p'=>$hash,':e'=>$data['email'],':n'=>$nombre,':a'=>$apellido,':id'=>$existente['id']]);
                return $existente['id'];
            }
        }

        $st = $this->db->prepare(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol, cedula, email_verificado)
             VALUES (:n, :a, :e, :p, :r, :c, FALSE) RETURNING id"
        );
        $st->execute([
            ':n'=>$nombre, ':a'=>$apellido, ':e'=>$data['email'],
            ':p'=>$hash, ':r'=>$data['rol'], ':c'=>$data['cedula'],
        ]);
        return $st->fetchColumn();
    }

    public function update(int $id, array $data): bool {
        $st = $this->db->prepare(
            "UPDATE usuarios SET nombre=:n, apellido=:a, email=:e, rol=:r, activo=:ac WHERE id=:id"
        );
        return $st->execute([
            ':n'=>$data['nombre'], ':a'=>$data['apellido'], ':e'=>$data['email'],
            ':r'=>$data['rol'], ':ac'=>(bool)$data['activo'], ':id'=>$id,
        ]);
    }

    public function updatePassword(int $id, string $password): bool {
        $hash = password_hash($password, PASSWORD_ARGON2ID);
        $st = $this->db->prepare("UPDATE usuarios SET password=:p WHERE id=:id");
        return $st->execute([':p'=>$hash, ':id'=>$id]);
    }

    public function updateFoto(int $id, string $foto): bool {
        $st = $this->db->prepare("UPDATE usuarios SET foto=:f WHERE id=:id");
        return $st->execute([':f'=>$foto, ':id'=>$id]);
    }

    public function delete(int $id): bool {
        $st = $this->db->prepare("UPDATE usuarios SET activo=FALSE WHERE id=:id");
        return $st->execute([':id'=>$id]);
    }

    public function createConMateria(array $data): int|false {
        $hash = password_hash($data['password'], PASSWORD_ARGON2ID);
        // Los profesores quedan pendientes hasta que completen su registro
        $verificado = ($data['rol'] === 'profesor') ? 'FALSE' : 'TRUE';
        $st = $this->db->prepare(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol, cedula, materia_id, email_verificado)
             VALUES (:n, :a, :e, :p, :r, :c, :m, $verificado) RETURNING id"
        );
        $st->execute([
            ':n'=>$data['nombre'], ':a'=>$data['apellido'], ':e'=>$data['email'] ?? '',
            ':p'=>$hash, ':r'=>$data['rol'], ':c'=>$data['cedula'] ?? null,
            ':m'=>$data['materia_id'] ?? null,
        ]);
        return $st->fetchColumn();
    }

    public function updateConMateria(int $id, array $data): bool {
        $st = $this->db->prepare(
            "UPDATE usuarios SET nombre=:n, apellido=:a, email=:e, rol=:r, activo=:ac, materia_id=:m WHERE id=:id"
        );
        return $st->execute([
            ':n'=>$data['nombre'], ':a'=>$data['apellido'], ':e'=>$data['email'],
            ':r'=>$data['rol'], ':ac'=>(bool)$data['activo'],
            ':m'=>$data['materia_id'] ?? null, ':id'=>$id,
        ]);
    }
}
