<?php

if(isset( $arrayPost['id']) && isset( $arrayPost['notificacion'])){
	$id = test_input($_POST['id']);
	$reason = test_input($_POST['notificacion']);
	require './mysqlData.php';

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	
	$reserve = "SELECT * FROM reserve_table WHERE id = $id;";
	$result = $conn->query($reserve);
	if ($result->num_rows === 1) {
		$row = $result->fetch_assoc();
	}else{
		die();
	}
	$sql = "DELETE FROM reserve_table WHERE id = $id;";
	
	if($conn->query($sql) === TRUE){
		require "send_email.php";
		
		$mail->addAddress($arrayPost['email'],$arrayPost['solicitante']);
		
		$mail->Subject = "Reserva para " . $arrayPost['name'] . ", " . $arrayPost['plane'] . " el " . date("Y-m-d", $arrayPost['startdate']) . " de " . date("H:i", $arrayPost['startdate']) . " a " . date("H:i", $arrayPost['enddate']);
		
		$mail->Body = (string) "Su reserva para " . $arrayPost['name'] . ", " . $arrayPost['plane'] . " el " . date("Y-m-d", $arrayPost['startdate']) . " de " . date("H:i", $arrayPost['startdate']) . " a " . date("H:i", $arrayPost['enddate']) . "," .  "<br/><br/>Para realizar el evento:<br/>" . $arrayPost['evento'] . "<br/><br/>";
		
		if($arrayPost['Observaciones'] !== 'Sin especificar'){
			$mail->Body = $mail->Body . "Con las observaciones: <br/>" . $arrayPost['Observaciones'] . "<br/><br/>";
		}
		
		if($reason === "")
			$mail->Body = $mail->Body . "Ha sido rechazada por el administrador.";
		else
			$mail->Body = $mail->Body . "Ha sido rechazado por la siguiente razón:<br/> " . $reason;
		 

		 
		if(!$mail->Send())
			$output = " Message was not sent <br />PHPMailer Error: " . $mail->ErrorInfo;
		else
			$output = " Mensaje enviado";
	}

	$conn->close();
}
	
	

?>