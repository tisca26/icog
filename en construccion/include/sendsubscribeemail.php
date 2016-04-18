<?php

require_once('phpmailer/class.phpmailer.php');

$mail = new PHPMailer();

if( isset( $_POST['widget-subscribe-form-email'] ) ) {
    if( $_POST['widget-subscribe-form-email'] != '' ) {

        $email = $_POST['widget-subscribe-form-email'];

        $subject = 'Subscribe me to the List';

        $toemail = 'info@icognitis.com'; // Your Email Address
        $toname = 'Info ICOGNITIS'; // Your Name

        $mail->SetFrom( $email , 'New Subscriber' );
        $mail->AddReplyTo( $email );
        $mail->AddAddress( $toemail , $toname );
        $mail->Subject = $subject;

        $email = isset($email) ? "Email: $email<br><br>" : '';

        $referrer = $_SERVER['HTTP_REFERER'] ? '<br><br><br>This Form was submitted from: ' . $_SERVER['HTTP_REFERER'] : '';

        $body = "$email $referrer";

        $mail->MsgHTML( $body );
        $sendEmail = $mail->Send();

        if( $sendEmail == true ):
            echo 'Has sido suscrito <strong>exitosamente</strong> a nuestra Mailing List.';
        else:
            echo 'El email <strong>no pudo</strong> ser enviado debido a un problema inesperado. Por favor inténtalo más tarde.<br /><br /><strong>Razón:</strong><br />' . $mail->ErrorInfo . '';
        endif;
    } else {
        echo 'Por favor <strong>ingresa</strong> todos los campos e intenta nuevamente.';
    }
} else {
    echo 'Un <strong>error inesperado</strong> ha ocurrido. Por favor inténtalo más tarde.';
}

?>