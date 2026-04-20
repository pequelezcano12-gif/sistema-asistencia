<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'sistema_asistencia');
define('DB_USER', 'postgres');
define('DB_PASS', '123');
define('BASE_URL', 'http://localhost/sistema-asistencia/public');
define('MAIL_DEV_MODE', false);
define('MAIL_HOST',      'sandbox.smtp.mailtrap.io');
define('MAIL_PORT',      2525);
define('MAIL_USER',      '9965ce089f00fb');
define('MAIL_PASS',      'dbbd7379d88718');
define('MAIL_FROM',      'noreply@asistenciaedu.com');
define('MAIL_FROM_NAME', 'AsistenciaEdu');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("SET client_encoding TO 'UTF8'");
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;color:red">
                <h3>Error de conexión a PostgreSQL</h3>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Verificá las credenciales en <code>config/database.php</code></p>
            </div>');
        }
    }
    return $pdo;
}
