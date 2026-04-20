<?php
require_once __DIR__ . '/../../config/database.php';

class Notificacion {
    private PDO $db;
    public function __construct() { $this->db = getDB(); }

    public function crear(int $usuario_id, string $tipo, string $titulo, string $mensaje = '', int $ref_id = null): void {
        $st = $this->db->prepare(
            "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, referencia_id)
             VALUES (:u, :t, :ti, :m, :r)"
        );
        $st->execute([':u'=>$usuario_id,':t'=>$tipo,':ti'=>$titulo,':m'=>$mensaje,':r'=>$ref_id]);
    }

    public function getNoLeidas(int $usuario_id): array {
        $st = $this->db->prepare(
            "SELECT * FROM notificaciones WHERE usuario_id=:u AND leida=FALSE ORDER BY created_at DESC"
        );
        $st->execute([':u'=>$usuario_id]);
        return $st->fetchAll();
    }

    public function getTodas(int $usuario_id, int $limit = 30): array {
        $st = $this->db->prepare(
            "SELECT * FROM notificaciones WHERE usuario_id=:u ORDER BY created_at DESC LIMIT :l"
        );
        $st->execute([':u'=>$usuario_id, ':l'=>$limit]);
        return $st->fetchAll();
    }

    public function marcarLeida(int $id, int $usuario_id): void {
        $st = $this->db->prepare("UPDATE notificaciones SET leida=TRUE WHERE id=:id AND usuario_id=:u");
        $st->execute([':id'=>$id, ':u'=>$usuario_id]);
    }

    public function marcarTodasLeidas(int $usuario_id): void {
        $st = $this->db->prepare("UPDATE notificaciones SET leida=TRUE WHERE usuario_id=:u");
        $st->execute([':u'=>$usuario_id]);
    }

    public function contarNoLeidas(int $usuario_id): int {
        $st = $this->db->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id=:u AND leida=FALSE");
        $st->execute([':u'=>$usuario_id]);
        return (int)$st->fetchColumn();
    }

    // Obtener IDs de directivos para notificarlos
    public function getDirectivos(): array {
        $st = $this->db->query("SELECT id FROM usuarios WHERE rol IN ('admin','directivo') AND activo=TRUE");
        return $st->fetchAll(PDO::FETCH_COLUMN);
    }
}
