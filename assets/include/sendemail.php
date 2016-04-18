<?php

require_once('phpmailer/PHPMailerAutoload.php');

$toemails = array();

$toemails[] = array(
    'email' => 'contacto@icognitis.com', // Your Email Address
    'name' => 'Contacto ICOGNITIS' // Your Name
);

// Form Processing Messages
$message_success = 'Hemos recibido <strong>satisfactoriamente</strong> su mensaje. Nos comunicaremos pronto con usted';

// Add this only if you use reCaptcha with your Contact Forms
$recaptcha_secret = '6LczdRwTAAAAAF3tl8D8tgaxh56OheEB7ahPvMhB'; // Your reCaptcha Secret

$mail = new PHPMailer();

// If you intend you use SMTP, add your SMTP Code after this Line


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['contactform-email'] != '') {

        $name = isset($_POST['contactform-name']) ? $_POST['contactform-name'] : '';
        $email = isset($_POST['contactform-email']) ? $_POST['contactform-email'] : '';
        $phone = isset($_POST['contactform-phone']) ? $_POST['contactform-phone'] : '';
        $location = isset($_POST['contactform-location']) ? $_POST['contactform-location'] : '';
        $subject = isset($_POST['contactform-subject']) ? $_POST['contactform-subject'] : '';
        $message = isset($_POST['contactform-message']) ? $_POST['contactform-message'] : '';

        $subject = isset($subject) ? 'Desde ICOGNITIS: ' . $subject : 'Desde ICOGNITIS: Correo de prospecto';
        
        $botcheck = $_POST['contactform-botcheck'];

        if ($botcheck == '') {

            $office = explode('-', $location);
            if ($office[0] == 'CDMX') {
                $toemails[] = array(
                    'email' => 'contactoDF@oficinaexpress.com.mx', // Your Email Address
                    'name' => 'Contacto OEN CDMX' // Your Name
                );
            } elseif ($office[0] == 'QRO') {
                $toemails[] = array(
                    'email' => 'contacto@oficinaexpress.com.mx', // Your Email Address
                    'name' => 'Contacto OEN QRO' // Your Name
                );
            } else {
                echo '{ "alert": "error", "message": "No se seleccionó ninguna sucursal" }';
                die;
            }

            $mail->SetFrom($email, $name);
            $mail->AddReplyTo($email, $name);
            foreach ($toemails as $toemail) {
                $mail->AddAddress($toemail['email'], $toemail['name']);
            }
            $mail->Subject = $subject;

            $name = isset($name) ? "Nombre: $name<br><br>" : '';
            $email = isset($email) ? "Email: $email<br><br>" : '';
            $phone = isset($phone) ? "Tel: $phone<br><br>" : '';
            $location = isset($location) ? "Sucursal: $location<br><br>" : '';
            $message = isset($message) ? "Mensaje: $message<br><br>" : '';

            $referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>Este correo fue enviado desde: ' . $_SERVER['HTTP_REFERER'] : '';

            $body = "$name $email $phone $location $message $referrer";

            // Runs only when File Field is present in the Contact Form
            if (isset($_FILES['contactform-file']) && $_FILES['contactform-file']['error'] == UPLOAD_ERR_OK) {
                $mail->IsHTML(true);
                $mail->AddAttachment($_FILES['contactform-file']['tmp_name'], $_FILES['contactform-file']['name']);
            }

            // Runs only when reCaptcha is present in the Contact Form
            if (isset($_POST['g-recaptcha-response'])) {
                $recaptcha_response = $_POST['g-recaptcha-response'];
                $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response);

                $g_response = json_decode($response);

                if ($g_response->success !== true) {
                    echo '{ "alert": "error", "message": "Captcha no válido! Por favor intente de nuevo." }';
                    die;
                }
            }

            $mail->MsgHTML($body);
            $sendEmail = $mail->Send();

            if ($sendEmail == true):
                echo '{ "alert": "success", "message": "' . $message_success . '" }';
            else:
                echo '{ "alert": "error", "message": "El email <strong>no pudo</strong> ser enviado por algún error inesperado. Por favor inténtelo más tarde.<br /><br /><strong>Reason:</strong><br />' . $mail->ErrorInfo . '" }';
            endif;
        } else {
            echo '{ "alert": "error", "message": "Bot <strong>detectado</strong>.¡No enviaremos nada!" }';
        }
    } else {
        echo '{ "alert": "error", "message": "Por favor <strong>llene</strong> todos los campos e intente de nuevo." }';
    }
} else {
    echo '{ "alert": "error", "message": "Un <strong>error inesperado</strong> ocurrió. Por favor inténtelo más tarde." }';
}
?>