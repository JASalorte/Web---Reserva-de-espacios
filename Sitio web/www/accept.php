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
	
	
	$userdata;
	$userdata['Nombre y apellidos'] = $arrayPost['solicitante'];
	$userdata['Edificio'] = $arrayPost['edificio'];
	$userdata['Dependencia'] = $arrayPost['dependencia'];
	$userdata['Sector'] = $arrayPost['sector'];
	$userdata['Servicio'] = $arrayPost['Servicio/Unidad'];
	$userdata['Centro/Departamento'] = $arrayPost['Departamento'];
	$userdata['E-mail'] = $arrayPost['email'];
	$userdata['Teléfono'] = $arrayPost['telefono'];
	$userdata['Móvil'] = $arrayPost['movil'];
	
	$eventdata;
	$eventdata['Evento a celebrar'] = $arrayPost['evento'];
	$eventdata['Observaciones'] = $arrayPost['Observaciones'];
	$eventdata['Hora de inicio del acto'] = $arrayPost['Hora de inicio del acto'];
	
	$result = $arrayPost['result'];
	
	
	$eventdata = str_replace("\\", "\\\\", json_encode($eventdata));
	$userdata = str_replace("\\", "\\\\", json_encode($userdata));
	
	$userdata = str_replace("'", "''",$userdata);
	$eventdata = str_replace("'", "''",$eventdata);
	$result = str_replace("'", "''",$result);
	
	$sql = "UPDATE reserve_table 
	SET state='Reserved'
	WHERE id = $id;";
	
	$sql = "UPDATE reserve_table 
	SET state='Reserved', 
	startdate = '" . $arrayPost['startdate'] . "'" . ",
	enddate = '" . $arrayPost['enddate'] . "'" . ",
	name = '" . $arrayPost['name'] . "'" . ",
	plane = '" . $arrayPost['plane'] . "'" . ",
	userinfo = '" . $userdata . "'" . ",
	eventinfo = '" . $eventdata . "'" . ",
	necesities = '" . $result . "'" . "
	WHERE id = $id;";

	if ($conn->query($sql) === TRUE) {
		require "send_email.php";
		
		$mail->addAddress($arrayPost['email'],$arrayPost['solicitante']);
		
		$mail->Subject = "Reserva para " . $arrayPost['name'] . ", " . $arrayPost['plane'] . " el " . date("Y-m-d", $arrayPost['startdate']) . " de " . date("H:i", $arrayPost['startdate']) . " a " . date("H:i", $arrayPost['enddate']);
		
		$mail->Body = (string) "Su reserva para " . $arrayPost['name'] . ", " . $arrayPost['plane'] . " el " . date("Y-m-d", $arrayPost['startdate']) . " de " . date("H:i", $arrayPost['startdate']) . " a " . date("H:i", $arrayPost['enddate']) . "," .  "<br/><br/>Para realizar el evento:<br/>" . $arrayPost['evento'] . "<br/><br/>";
		
		if($arrayPost['Observaciones'] !== 'Sin especificar'){
			$mail->Body = $mail->Body . "Con las observaciones: <br/>" . $arrayPost['Observaciones'] . "<br/><br/>";
		}
		
		if($reason === "")
			$mail->Body = $mail->Body . "Ha sido aceptada por el administrador.";
		else
			$mail->Body = $mail->Body . "Ha sido aceptada y el administrador le ha escrito una nota:<br/> " . $reason;
		 
		if(!$mail->Send())
			$output = " Message was not sent <br />PHPMailer Error: " . $mail->ErrorInfo;
		else
			$output = " Mensaje enviado";
	
	
	
	
	
	}

	$conn->close();
}
	
	

?>