<?php
/**
 * Security Helper — CSRF, Rate Limiting, Tokens, Headers
 */
class Security {

    // ── CSRF ────────────────────────────────────────────────────────────────
    public static function csrfToken(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . self::csrfToken() . '">';
    }

    public static function verifyCsrf(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die('<div style="font-family:sans-serif;padding:2rem;color:red">
                <h3>⛔ Solicitud inválida (CSRF)</h3>
                <a href="javascript:history.back()">Volver</a>
            </div>');
        }
        // Rotar token después de verificar
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // ── Rate Limiting ────────────────────────────────────────────────────────
    public static function checkRateLimit(string $cedula = ''): void {
        $db = getDB();
        $ip = self::getIP();
        $maxIntentos = 5;
        $bloqueoMinutos = 15;

        $st = $db->prepare(
            "SELECT intentos, bloqueado_hasta FROM login_attempts
             WHERE ip = :ip AND (cedula = :c OR cedula IS NULL)
             ORDER BY ultimo_intento DESC LIMIT 1"
        );
        $st->execute([':ip' => $ip, ':c' => $cedula]);
        $row = $st->fetch();

        if ($row && $row['bloqueado_hasta'] && strtotime($row['bloqueado_hasta']) > time()) {
            $resta = ceil((strtotime($row['bloqueado_hasta']) - time()) / 60);
            self::dieBlocked($resta);
        }
    }

    public static function registerFailedAttempt(string $cedula = ''): void {
        $db = getDB();
        $ip = self::getIP();
        $maxIntentos = 5;
        $bloqueoMinutos = 15;

        $st = $db->prepare(
            "SELECT id, intentos FROM login_attempts WHERE ip = :ip ORDER BY ultimo_intento DESC LIMIT 1"
        );
        $st->execute([':ip' => $ip]);
        $row = $st->fetch();

        if ($row) {
            $nuevos = $row['intentos'] + 1;
            $bloqueo = $nuevos >= $maxIntentos
                ? "NOW() + INTERVAL '$bloqueoMinutos minutes'"
                : 'NULL';
            $db->prepare(
                "UPDATE login_attempts SET intentos=:i, cedula=:c, ultimo_intento=NOW(),
                 bloqueado_hasta=" . ($nuevos >= $maxIntentos ? "NOW() + INTERVAL '$bloqueoMinutos minutes'" : "NULL") . "
                 WHERE id=:id"
            )->execute([':i' => $nuevos, ':c' => $cedula, ':id' => $row['id']]);
        } else {
            $db->prepare(
                "INSERT INTO login_attempts (ip, cedula, intentos) VALUES (:ip, :c, 1)"
            )->execute([':ip' => $ip, ':c' => $cedula]);
        }
    }

    public static function clearAttempts(string $cedula = ''): void {
        $db = getDB();
        $ip = self::getIP();
        $db->prepare("DELETE FROM login_attempts WHERE ip = :ip")
           ->execute([':ip' => $ip]);
    }

    private static function dieBlocked(int $minutos): void {
        http_response_code(429);
        die('<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">
        <title>Bloqueado</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        </head><body class="bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="min-height:100vh">
        <div class="card shadow p-5 text-center" style="max-width:400px">
            <div style="font-size:3rem">🔒</div>
            <h4 class="mt-3 fw-bold text-danger">Acceso bloqueado</h4>
            <p class="text-muted">Demasiados intentos fallidos.<br>
            Intentá de nuevo en <strong>' . $minutos . ' minuto(s)</strong>.</p>
            <a href="' . BASE_URL . '/login" class="btn btn-outline-danger mt-2">Volver al inicio</a>
        </div></body></html>');
    }

    // ── Tokens seguros ───────────────────────────────────────────────────────
    public static function generateToken(int $bytes = 32): string {
        return bin2hex(random_bytes($bytes));
    }

    public static function generateCode(int $digits = 6): string {
        return str_pad((string)random_int(0, 999999), $digits, '0', STR_PAD_LEFT);
    }

    // ── Headers de seguridad HTTP ────────────────────────────────────────────
    public static function setSecurityHeaders(): void {
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'");
    }

    // ── Utilidades ───────────────────────────────────────────────────────────
    public static function getIP(): string {
        foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return '0.0.0.0';
    }

    public static function sanitize(string $val): string {
        return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
    }

    public static function validarCedula(string $cedula): bool {
        // Acepta cédulas ecuatorianas (10 dígitos) o pasaportes alfanuméricos
        return preg_match('/^[A-Z0-9]{5,20}$/i', $cedula) === 1;
    }
}
