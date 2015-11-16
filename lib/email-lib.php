<?php

require 'phpmailer/PHPMailerAutoload.php';

function sendWelcomeEmail() {
	return;
	
	$mail = new PHPMailer;

	$mail->isSMTP();
	$mail->SMTPDebug = 0; // Debug mode off; 2 = ON

	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 587;
	$mail->SMTPSecure = 'tls';
	$mail->SMTPAuth = true;
	$mail->Username = 'themartaarmy@gmail.com';
	$mail->Password = 'itsMARTA123';

	$mail->FromName = 'The MARTA Army';
	$mail->addAddress($email, $name);     // Add a recipient
	$mail->addBCC('themartaarmy@gmail.com');

	$mail->isHTML(true);

	$mail->Subject = "Hi $name! Welcome to the MARTA Army!";
	$mail->Body = 
		"Hi $name,<br/><br/>Thank you for joining the MARTA Army and volunteering to adopt bus stops!<br/><br/>" .
		"The next step is for us to get you the signage for the bus stops you've chosen to adopt. We will do so at ".
		"events that we conduct frequently throughout Atlanta. <br/>".
		"We'll get in touch with you soon and let you know about our upcoming events. We hope you'll be able to attend one, meet more concerned citizens like yourself, and collect the signs as well.<br/><br/> ".
		"If ever you have questiosn about this, just shoot us an email at themartaarmy@gmail.com, or just reply to this email.<br/><br/>".
		"Keep Marching<br/>".
		"The MARTA Army";

	return $mail->send();
}

function sendNewStopsAdoptedEmail() {

}

?>