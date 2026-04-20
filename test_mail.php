<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/helpers/Mailer.php';

$r = Mailer::enviarCodigoReset('test@test.com', 'Usuario Prueba', '123456');
echo $r ? 'EMAIL ENVIADO OK — revisá Mailtrap' : 'ERROR AL ENVIAR — revisá storage/logs/emails.log';
