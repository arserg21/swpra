<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../lib/phpmailer664/src/Exception.php';
require '../lib/phpmailer664/src/PHPMailer.php';
require '../lib/phpmailer664/src/SMTP.php';

abstract class SWPRAEmailer {

    private const HOST = 'smtp.gmail.com';
    private const EMAIL_SERVIDOR = 'swpra.contacto@gmail.com';
    private const EMAIL_CONTRASENA = 'owwzpokhdbnxzzvz';
    private const PUERTO = 587;

    public function __construct() {
    }

    public static function enviar($email = '', $nombre = '', $codigo = '') {
        $mensaje = "
            <h1>Finaliza tu registro</h1>
            <p>Estimado $nombre usa el siguiente código para confirma tú dirección de correo electrónico.</p>
            <p><b>Código: </b>$codigo</p>
        ";
        $mail = new PHPMailer(true);
        try {
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host = SWPRAEmailer::HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SWPRAEmailer::EMAIL_SERVIDOR;
            $mail->Password = SWPRAEmailer::EMAIL_CONTRASENA;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SWPRAEmailer::PUERTO;
        
            $mail->setFrom(SWPRAEmailer::EMAIL_SERVIDOR, 'SWPRA');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Finaliza tu registro.';
            $mail->Body = $mensaje;
            $mail->send();
            //echo "Mensaje enviado";
        } catch (Exception $ex) {
            //echo $mail->ErrorInfo;
            //throw new Exception('Código de confirmación no enviado.');
        }
    }
}


?>