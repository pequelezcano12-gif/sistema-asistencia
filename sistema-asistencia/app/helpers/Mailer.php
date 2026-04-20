<?php
/**
 * Mailer simple — usa mail() de PHP o simula en desarrollo
 * En producción reemplazar con PHPMailer/SMTP
 */
class Mailer {

    public static function enviarCodigoReset(string $email, string $nombre, string $codigo): bool {
        $asunto  = 'Código para restablecer tu contraseña — AsistenciaEdu';
        $cuerpo  = self::template('Restablecer contraseña', $nombre,
            "Tu código de verificación es:",
            "<div style='font-size:2.5rem;font-weight:bold;letter-spacing:.5rem;color:#1a73e8;text-align:center;padding:1rem;background:#f0f4ff;border-radius:.5rem'>$codigo</div>",
            "Este código expira en <strong>15 minutos</strong>. Si no solicitaste esto, ignorá este mensaje."
        );
        return self::send($email, $asunto, $cuerpo);
    }

    public static function enviarCodigoVerificacion(string $email, string $nombre, string $codigo): bool {
        $asunto = 'Verificá tu cuenta — AsistenciaEdu';
        $cuerpo = self::template('Verificación de cuenta', $nombre,
            "Tu código de verificación es:",
            "<div style='font-size:2.5rem;font-weight:bold;letter-spacing:.5rem;color:#1a73e8;text-align:center;padding:1rem;background:#f0f4ff;border-radius:.5rem'>$codigo</div>",
            "Este código expira en <strong>30 minutos</strong>."
        );
        return self::send($email, $asunto, $cuerpo);
    }

    private static function template(string $titulo, string $nombre, string $texto1, string $contenido, string $texto2): string {
        return "<!DOCTYPE html><html><head><meta charset='UTF-8'></head>
        <body style='font-family:Segoe UI,sans-serif;background:#f5f5f5;padding:2rem'>
        <div style='max-width:480px;margin:auto;background:#fff;border-radius:1rem;padding:2rem;box-shadow:0 4px 20px rgba(0,0,0,.1)'>
            <div style='text-align:center;margin-bottom:1.5rem'>
                <span style='font-size:2rem'>📋</span>
                <h2 style='color:#1a73e8;margin:.5rem 0'>AsistenciaEdu</h2>
            </div>
            <h3 style='color:#333'>$titulo</h3>
            <p>Hola <strong>" . htmlspecialchars($nombre) . "</strong>,</p>
            <p>$texto1</p>
            $contenido
            <p style='color:#666;font-size:.9rem;margin-top:1.5rem'>$texto2</p>
            <hr style='border:none;border-top:1px solid #eee;margin:1.5rem 0'>
            <p style='color:#999;font-size:.8rem;text-align:center'>AsistenciaEdu &copy; " . date('Y') . "</p>
        </div></body></html>";
    }

    private static function send(string $to, string $subject, string $body): bool {
        // En desarrollo: guardar en archivo de log en vez de enviar
        if (defined('MAIL_DEV_MODE') && MAIL_DEV_MODE) {
            $log = date('Y-m-d H:i:s') . " | TO: $to | SUBJECT: $subject\n";
            // Extraer código del body para mostrarlo fácilmente
            preg_match('/\d{6}/', strip_tags($body), $m);
            $log .= "CODIGO: " . ($m[0] ?? 'N/A') . "\n---\n";
            file_put_contents(__DIR__ . '/../../storage/logs/emails.log', $log, FILE_APPEND);
            return true;
        }

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: AsistenciaEdu <noreply@asistenciaedu.com>\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        return mail($to, $subject, $body, $headers);
    }
}
