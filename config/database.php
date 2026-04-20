<?php
// Parsear DATABASE_URL de Railway si existe
$dbUrl = getenv('DATABASE_URL');
if ($dbUrl) {
    $parts = parse_url($dbUrl);
    define('DB_HOST', $parts['host']);
    define('DB_PORT', $parts['port'] ?? 5432);
    define('DB_NAME', ltrim($parts['path'], '/'));
    define('DB_USER', $parts['user']);
    define('DB_PASS', $parts['pass']);
} else {
    define('DB_HOST', getenv('PGHOST')     ?: 'localhost');
    define('DB_PORT', getenv('PGPORT')     ?: '5432');
    define('DB_NAME', getenv('PGDATABASE') ?: 'sistema_asistencia');
    define('DB_USER', getenv('PGUSER')     ?: 'postgres');
    define('DB_PASS', getenv('PGPASSWORD') ?: '123');
}

$domain = getenv('RAILWAY_PUBLIC_DOMAIN') ?: getenv('RAILWAY_STATIC_URL') ?: '';
if ($domain) {
    define('BASE_URL', 'https://' . $domain);
} else {
    // Detectar dominio automáticamente desde la request
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    define('BASE_URL', $scheme . '://' . $host);
}

define('MAIL_DEV_MODE', false);
define('MAIL_HOST',      getenv('MAIL_HOST')      ?: 'smtp-relay.brevo.com');
define('MAIL_PORT',      (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USER',      getenv('MAIL_USER')      ?: '');
define('MAIL_PASS',      getenv('MAIL_PASS')      ?: '');
define('MAIL_FROM',      getenv('MAIL_FROM')      ?: '');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'AsistenciaEdu');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("SET client_encoding TO 'UTF8'");
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;color:red"><h3>Error de conexion</h3><p>' . htmlspecialchars($e->getMessage()) . '</p></div>');
        }
    }
    return $pdo;
}
