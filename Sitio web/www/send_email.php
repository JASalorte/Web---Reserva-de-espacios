<?php

require 'PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Mailer = 'smtp';
$mail->SMTPAuth = true;
$mail->Host = 'smtp.gmail.com'; // "ssl://smtp.gmail.com" didn't worked
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
$mail->CharSet = 'UTF-8';
// or try these settings (worked on XAMPP and WAMP):
// $mail->Port = 587;
// $mail->SMTPSecure = 'tls';
 
 
$mail->IsHTML(true); // if you are going to send HTML formatted emails
$mail->SingleTo = true; // if you want to send a same email to multiple users. multiple emails will be sent one-by-one.
 
 
/* CONFIGURACIÓN SMTP */
 
/*Para poder enviar correo primero hace falta una cuenta base.
IMPORTANTE!
Para que esta cuenta funcione tiene que tener el IMAP activado!
*/

$mail->Username = "reservotalentum@gmail.com";
$mail->Password = "Tale4phone";

/*Este es el correo y nombre del emisor que le aparecerá al usuario.*/
$mail->From = "reservotalentum@gmail.com";
$mail->FromName = "Talentum Reservas";

/*Esto envía una copia del correo enviado al usuario a la cuenta introducida.*/
$mail->addBCC("reservotalentum@gmail.com","Talentum Reservas");

/*FIN CONFIGURACIÓN*/
 
?>