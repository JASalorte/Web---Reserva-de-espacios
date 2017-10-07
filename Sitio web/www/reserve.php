<?php
/* * * begin the session ** */
session_start();

function form_value($name, $type = 'text', $select_value = '') {
    if (isset($_POST[$name])) {
        switch ($type) {
            case 'text': {
                    return ' value="' . htmlspecialchars($_POST[$name]) . '" ';
                    break;
                }
            case 'textarea': {
                    return htmlspecialchars($_POST[$name]);
                    break;
                }
            case 'checkbox': {
                    return ' checked="true" ';
                    break;
                }
            case 'radio': {
                    if ($_POST[$name] == $select_value) {
                        return ' checked="checked" ';
                    }
                    break;
                }
            case 'select': {
                    if ($_POST[$name] == $select_value) {
                        return ' selected="selected" ';
                    }
                    break;
                }
        }//switch
    }
    return '';
}

if (!isset($_SESSION['user_id'])) {
    $message = 'You must be logged in to access this page';
} else {
    try {
        require './mysqlData.php';


        /*         * * select the users name from the database ** */
        $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        /*         * * $message = a message saying we have connected ** */

        /*         * * set the error mode to excptions ** */
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*         * * prepare the insert ** */
        $stmt = $dbh->prepare("SELECT username FROM users 
        WHERE id = :id");

        /*         * * bind the parameters ** */
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        /*         * * execute the prepared statement ** */
        $stmt->execute();

        /*         * * check for a result ** */
        $phpro_username = $stmt->fetchColumn();
        $_SESSION['user_name'] = $phpro_username;

        /*         * * if we have no something is wrong ** */
    } catch (Exception $e) {
        
    }
}
?>

<?php
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require 'phpdata.php';

    $nombre = $edificio = $dependencia = $sector = $servicio = $departamento = $email = $telefono = $movil = "";
    $espacio = $node = $startdate = $enddate = $eventdate = $event = $observaciones = $num_per_mesa_presi = $botellas = $site = "";


    if (empty($_POST["solicitante"])) {
        $error = $error . ", El nombre es necesario";
    } else {
        $nombre = test_input($_POST["solicitante"]);
        if (!preg_match("/^[a-z .\-áéíóúÁÉÍÓÚ]+$/i", $nombre)) {
            $error = $error . ", Solo se admiten letras y espacios en el nombre";
        }
        if (strlen($nombre) > 70) {
            $error = $error . ", El nombre es demasiado largo";
        }
    }

    if ($_POST["edificio"] === "-") {
        $error = $error . ", El campo edificio es obligatorio";
    } else {
        $edificio = test_input($_POST["edificio"]);
        if (!in_array($edificio, $arrayEdificio)) {
            $error = $error . ", El edificio seleccionado no existe";
        }
    }

    if (empty($_POST["dependencia"])) {
        $error = $error . ", El campo dependencia es obligatorio";
    } else {
        $dependencia = test_input($_POST["dependencia"]);
        if (!preg_match("/[0-9][0-9][0-9]/", $dependencia)) {
            $error = $error . ", La dependencia es incorrecta";
        }
    }

    if ($_POST["sector"] === "-") {
        $error = $error . ", El campo sector es obligatorio";
    } else {
        $sector = test_input($_POST["sector"]);
        if (!in_array($sector, $arraySector)) {
            $error = $error . ", El sector seleccionado no existe";
        }
    }

    if ($_POST["Servicio/Unidad"] === "Indique Órgano de Gobierno/Servicio") {
        $servicio = "Sin especificar";
    } else {
        $servicio = test_input($_POST["Servicio/Unidad"]);
    }

    if ($_POST["Departamento"] === "Indique su Centro/Departamento") {
        $departamento = "Sin especificar";
    } else {
        $departamento = test_input($_POST["Departamento"]);
    }

    if ((empty($_POST["email"])) || test_input($_POST["email"]) === "@ujaen.es") {
        $error = $error . ", Es necesario introducir el email";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = $error . ", Formato de email incorrecto";
        }
        if (!strpos($email, "ujaen.es")) {
            $error = $error . ", El correo facilitado no es de la Ujaen";
        }
    }

    if (empty($_POST["telefono"])) {
        $error = $error . ", Es necesario introducir un teléfono";
    } else {
        $telefono = test_input($_POST["telefono"]);
        if (!preg_match("/[+]?[0-9]{7,15}/", $telefono)) {
            $error = $error . ", Introduzca un número de teléfono correcto";
        }
    }

    if (empty($_POST["movil"])) {
        $movil = "Sin especificar";
    } else {
        $movil = test_input($_POST["movil"]);
        if (!preg_match("/[+]?[0-9]{7,15}/", $movil)) {
            $error = $error . ", Introduzca un número de móvil correcto";
        }
    }

    if (empty($_POST["espacio"])) {
        $error = $error . ", El espacio no está definido, ha ocurrido algún error";
    } else {
        $espacio = test_input($_POST["espacio"]);
        $node = array_search($espacio, $arrayEspacios);
        //echo $espacio . "<br/>" . $arrayEspacios[0] . "<br/>" . $node . "<br/>";
        if ($node === false) {
            $error = $error . ", El espacio seleccionado no existe, ha ocurrido algún error";
        }
    }

    if (empty($_POST["startdate"])) {
        $error = $error . ", El fecha de inicio no está definida, ha ocurrido algún error";
    } else {
        $startdate = test_input($_POST["startdate"]);
        if (!strtotime($startdate)) {
            $error = $error . ", La fecha de inicio es incorrecta, ha ocurrido algún error";
        } else {
            $bookdate = explode(" ", $startdate)[0];
        }
        //echo $startdate . "<br/>";
    }

    if (empty($_POST["hora_finalizacion"])) {
        $error = $error . ", El fecha de fin no está definida, ha ocurrido algún error";
    } else {
        $enddate = test_input($_POST["hora_finalizacion"]);
        $enddate = $bookdate . " " . $enddate . ":00";
        if (!strtotime($enddate)) {
            $error = $error . ", La fecha de fin es incorrecta, ha ocurrido algún error";
        }
    }

    if (empty($_POST["hora_acto"])) {
        $error = $error . ", El fecha del acto no está definida, ha ocurrido algún error";
    } else {
        $eventdate = test_input($_POST["hora_acto"]);
        $eventdate = $bookdate . " " . $eventdate . ":00";
        if (!strtotime($eventdate)) {
            $error = $error . ", La fecha del acto es incorrecta, ha ocurrido algún error";
        }
    }

    if (empty($_POST["evento"])) {
        $error = $error . ", Es necesario introducir una descripción del evento a realizar";
    } else {
        $event = test_input($_POST["evento"]);
    }

    if (empty($_POST["observaciones"])) {
        $observaciones = "Sin especificar";
    } else {
        $observaciones = test_input($_POST["observaciones"]);
    }

    if (is_numeric(test_input($_POST["num_personas_mesa_presidencial"]))) {
        $num_per_mesa_presi = test_input($_POST["num_personas_mesa_presidencial"]);
    } else {
        $num_per_mesa_presi = 0;
    }
    if (is_numeric(test_input($_POST["botellas"]))) {
        $botellas = test_input($_POST["botellas"]);
    } else {
        $botellas = 0;
    }

    if (empty($_POST["site"])) {
        $error = $error . ", Ha ocurrido un error inesperado";
    } else {
        $site = test_input($_POST["site"]);
    }



    if ($error !== "") {
        //echo "No se ha hecho na de na<br/>";
    } else {
        require './mysqlData.php';

        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT COUNT(id) solapacion FROM reserve_table "
                . "WHERE plane='$site' AND "
                . "name='$espacio' AND "
                . "((UNIX_TIMESTAMP('$startdate') > startdate AND UNIX_TIMESTAMP('$startdate') < enddate) OR "
                . "(UNIX_TIMESTAMP('$enddate') > startdate AND UNIX_TIMESTAMP('$enddate') < enddate));";

        //echo $sql . "<br/>";
        $result = $conn->query($sql);
        $return;

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $return = $row;
            }
        }

        if (!($return['solapacion'] === "0")) {
            $error = $error . ", Parece que alguien acaba de reservar este espacio";
            //echo "Hay dos reservas en el mismo espacio de tiempo!";
        } else {
            $error = "";
            //echo "Guay <br/>";
        }


        $check = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $checkData = [0, 0];
        foreach ($_POST as $key => $value) {
            $pos = array_search($key, $arrCheck);
            if ($pos !== false) {
                if ($key === "sin_necesidades") {
                    $check = [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    break;
                }

                if ($pos > 13) {
                    if ($value === "") {
                        $value = "0";
                    }
                    $checkData[array_search($key, $arrCheckEsp)] = $value;
                } else {
                    $check[$pos] = 1;
                }
            }
        }

        $result = "";
        for ($i = 0; $i < count($check); $i++) {
            $result = $result . $check[$i];
        }

        for ($i = 0; $i < count($checkData); $i++) {
            $result = $result . " " . $checkData[$i];
        }

        if ($error === "") {
            /*require "send_email.php";

            $mail->addAddress($email, $nombre);

            $mail->Subject = "Reserva para " . $site . ", " . $espacio . " el " . date("Y-m-d", $startdate) . " de " . date("H:i", $startdate) . " a " . date("H:i", $enddate);

            $mail->Body = (string) "Su reserva para " . $site . ", " . $espacio . " el " . date("Y-m-d", $startdate) . " de " . date("H:i", $startdate) . " a " . date("H:i", $enddate) . "," . "<br/><br/>Para realizar el evento:<br/>" . $event . "<br/><br/>";

            if ($eventdata['Observaciones'] !== 'Sin especificar') {
                $mail->Body = $mail->Body . "Con las observaciones: <br/>" . $observaciones . "<br/><br/>";
            }


            $mail->Body = $mail->Body . "Este es un mensaje de confirmación de que su reserva ha llegado correctamente, ahora tiene que ser validada por un administrador.";


            if (!$mail->Send())
                $output = " El mensaje no se ha enviado <br />PHPMailer Error: " . $mail->ErrorInfo;
            else
                $output = " Mensaje enviado";



            $conn->close();
            
            */

            $userdata;
            $userdata['Nombre y apellidos'] = $nombre;
            $userdata['Edificio'] = $edificio;
            $userdata['Dependencia'] = $dependencia;
            $userdata['Sector'] = $sector;
            $userdata['Servicio'] = $servicio;
            $userdata['Centro/Departamento'] = $departamento;
            $userdata['E-mail'] = $email;
            $userdata['Teléfono'] = $telefono;
            $userdata['Móvil'] = $movil;


            $userdata = str_replace("\\", "\\\\", json_encode($userdata));

            $eventdata;
            $eventdata['Evento a celebrar'] = $event;
            $eventdata['Observaciones'] = $observaciones;
            $eventdata['Hora de inicio del acto'] = strtotime($eventdate);
            $eventdata = str_replace("\\", "\\\\", json_encode($eventdata));

            //Sealing string
            $site = str_replace("'", "''", $site);
            $espacio = str_replace("'", "''", $espacio);
            $userdata = str_replace("'", "''", $userdata);
            $eventdata = str_replace("'", "''", $eventdata);
            $result = str_replace("'", "''", $result);
            $node = str_replace("'", "''", $node);
            $startdate = str_replace("'", "''", $startdate);
            $enddate = str_replace("'", "''", $enddate);



            $sql = "INSERT INTO reserve_table "
                    . "VALUES (null, 'Universidad de Jaén', '$site','$espacio', $node, UNIX_TIMESTAMP('$startdate'), UNIX_TIMESTAMP('$enddate'),"
                    . "'Revising', '$userdata','$eventdata','$result');";

            //echo $sql . "<br/>";
            $result = $conn->query($sql);

            //require './confirmation.php';
            
        } else {
            //echo "No se ha hecho";
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="resources.js"></script>
        <script src="jquery-1.11.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="CSS/cerulean.css">

        <title>Gestión de espacio - Reserva</title>


    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Reservas Ujaen</a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="/">Reservar<span class="sr-only">(current)</span></a></li>
<?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="validate.php">Solicitudes</a></li>
                            <li><a href="acepted.php">Aceptadas</a></li>
                        <?php endif; ?>
                    </ul>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="#" onclick="logout()" >Cerrar sesión</a></li>
                        </ul>
<?php else: ?>

                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="login.php">Iniciar sesión</a></li>
                        </ul>
<?php endif; ?>
                </div>
            </div>
        </nav>



<?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-0"></div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

    <?php if ($error === ""): ?>
        <?php $success = true; ?>
                            <?php if (isset($output)): ?>    
                                <div id="popup" class="alert alert-dismissible alert-info">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                <?php echo $output; ?>
                                </div>
                                <?php endif; ?>
                            <div class="alert alert-dismissible alert-success">
                                Su reserva se ha guardado correctamente, el administrador le enviará un email al correo proporcionado con la confirmación de su reserva, <a href="/" class="alert-link">pulse aquí para ir a la página principal</a>.
                            </div>
    <?php else: ?>
                            <div class="alert alert-dismissible alert-warning">
                                <button type="button" class="close" data-dismiss="alert">-</button>
                            <?php echo substr($error, 2); ?>
                            </div>        

                            <span class="error"></span>
    <?php endif; ?>
                    </div>
                </div>
            </div>
<?php endif; ?>

        <?php if (!isset($success)): ?>

            <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-2 col-md-1 col-sm-1 col-xs-0"></div>

                    <div class="col-lg-8 col-md-10 col-sm-10 col-xs-12">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <b id="titleBook" class="panel-title">Reserva</b>
                                    </div>
                                    <div class="panel-body">



                                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); ?>"  name="FrontPage_Form1" language="JavaScript">

                                            <input type="hidden" id="startdate" name="startdate">
                                            <input type="hidden" id="enddate" name="enddate">
                                            <input type="hidden" id="site" name="site">

                                            <p align="center"><font size="3"><b>DATOS DEL SOLICITANTE:</b></font></p>
                                            <p align="center" style="text-align: left"><b>Nombre y apellidos</b>:
                                                <br/><input type="text" name="solicitante" id="solicitante" size="57" <?php echo form_value('solicitante', 'text'); ?> ></p>
                                            <p align="center" style="text-align: left"><b>Edificio:</b>&nbsp;<!--webbot bot="Validation" b-value-required="TRUE" b-disallow-first-item="TRUE" --><select size="1" name="edificio" id="edificio">
                                                    <option <?php echo form_value('edificio', 'select', '-'); ?>>-</option>
                                                    <option <?php echo form_value('edificio', 'select', 'A1'); ?>>A1</option>
                                                    <option <?php echo form_value('edificio', 'select', 'A2'); ?>>A2</option>
                                                    <option <?php echo form_value('edificio', 'select', 'A3'); ?>>A3</option>
                                                    <option <?php echo form_value('edificio', 'select', 'A4'); ?>>A4</option>
                                                    <option <?php echo form_value('edificio', 'select', 'B1'); ?>>B1</option>
                                                    <option <?php echo form_value('edificio', 'select', 'B2'); ?>>B2</option>
                                                    <option <?php echo form_value('edificio', 'select', 'B3'); ?>>B3</option>
                                                    <option <?php echo form_value('edificio', 'select', 'B4'); ?>>B4</option>
                                                    <option <?php echo form_value('edificio', 'select', 'B5'); ?>>B5</option>
                                                    <option <?php echo form_value('edificio', 'select', 'C1'); ?>>C1</option>
                                                    <option <?php echo form_value('edificio', 'select', 'C2'); ?>>C2</option>
                                                    <option <?php echo form_value('edificio', 'select', 'C3'); ?>>C3</option>
                                                    <option <?php echo form_value('edificio', 'select', 'C4'); ?>>C4</option>
                                                    <option <?php echo form_value('edificio', 'select', 'C5'); ?>>C5</option>
                                                    <option <?php echo form_value('edificio', 'select', 'C6'); ?>>C6</option>
                                                    <option <?php echo form_value('edificio', 'select', 'D1'); ?>>D1</option>
                                                    <option <?php echo form_value('edificio', 'select', 'D2'); ?>>D2</option>
                                                    <option <?php echo form_value('edificio', 'select', 'D3'); ?>>D3</option>
                                                    <option <?php echo form_value('edificio', 'select', 'Magisterio'); ?>>Magisterio</option>
                                                    <option <?php echo form_value('edificio', 'select', 'A'); ?>>A</option>
                                                    <option <?php echo form_value('edificio', 'select', 'B'); ?>>B</option>
                                                </select>
                                                <b>&nbsp;&nbsp; Dependencia:</b>&nbsp;<input type="text" name="dependencia" <?php echo form_value('dependencia'); ?>  onkeypress="return soloNumeros(event)" maxlength="3" id="dependencia" size="7">&nbsp;&nbsp;&nbsp;&nbsp;
                                                <b>&nbsp;&nbsp; Sector: </b>
                                                <select size="1" name="sector" id="sector">
                                                    <option <?php echo form_value('sector', 'select', '-'); ?>>-</option>
                                                    <option <?php echo form_value('sector', 'select', 'PDI'); ?>>PDI</option>
                                                    <option <?php echo form_value('sector', 'select', 'PAS'); ?>>PAS</option></select></p>
                                            <p align="center" style="text-align: left"><b>Servicio:</b>
                                                <select size="1" name="Servicio/Unidad">
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Indique Órgano de Gobierno/Servicio'); ?>>Indique Órgano de Gobierno/Servicio</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Consejo Social'); ?>>Consejo Social</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Enseñanzas de Grado, Postgrado y Formación Permanente'); ?>>Vicerrectorado de Enseñanzas de Grado, Postgrado y Formación Permanente</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Investigación'); ?>>Vicerrectorado de Investigación</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Proyección de la Cultura, Deportes y Responsabilidad Social'); ?>>Vicerrectorado de Proyección de la Cultura, Deportes y Responsabilidad Social</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Profesorado y Ordenación Académica'); ?>>Vicerrectorado de Profesorado y Ordenación Académica</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Estudiantes'); ?>>Vicerrectorado de Estudiantes</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Tecnologías de la Información y la Comunicación e Infraestructuras'); ?>>Vicerrectorado de Tecnologías de la Información y la Comunicación e Infraestructuras</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Internacionalización'); ?>>Vicerrectorado de Internacionalización</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Vicerrectorado de Relaciones con la Sociedad e Inserción Laboral'); ?>>Vicerrectorado de Relaciones con la Sociedad e Inserción Laboral</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Gerencia'); ?>>Gerencia</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Secretaria General'); ?>>Secretaria General</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Gabinete del Rector'); ?>>Gabinete del Rector</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Asuntos Económicos'); ?>>Servicio de Asuntos Económicos</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Gestión Académica'); ?>>Servicio de Gestión Académica</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Atención y Ayudas al Estudiante'); ?>>Servicio de Atención y Ayudas al Estudiante</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Investigación'); ?>>Servicio de Investigación</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Unidad de Servicios Técnicos de Investigación'); ?>>Unidad de Servicios Técnicos de Investigación</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Negociados Apoyo a Departamentos y a Institutos y Centros de Investigación'); ?>>Negociados Apoyo a Departamentos y a Institutos y Centros de Investigación</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Técnicos Laboratorios de Departamentos y de Institutos y Centros de Investigación'); ?>>Técnicos Laboratorios de Departamentos y de Institutos y Centros de Investigación</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Personal y Organización Docente'); ?>>Servicio de Personal y Organización Docente</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Unidad de Conserjerías'); ?>>Unidad de Conserjerías</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Control Interno'); ?>>Servicio de Control Interno</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Contabilidad y Presupuestos'); ?>>Servicio de Contabilidad y Presupuestos</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Contratación y Patrimonio'); ?>>Servicio de Contratación y Patrimonio</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Obras, Mantenimiento y Vigilancia de las Instalaciones'); ?>>Servicio de Obras, Mantenimiento y Vigilancia de las Instalaciones</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Planificación y Evaluación'); ?>>Servicio de Planificación y Evaluación</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Información y Asuntos Generales'); ?>>Servicio de Información y Asuntos Generales</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Unidad de Actividades Culturales'); ?>>Unidad de Actividades Culturales</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Sección de Publicaciones'); ?>>Sección de Publicaciones</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Informática'); ?>>Servicio de Informática</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Centro de Instrumentación Científico-Técnica'); ?>>Centro de Instrumentación Científico-Técnica</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Biblioteca'); ?>>Servicio de Biblioteca</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Archivo General'); ?>>Servicio de Archivo General</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Servicio de Deportes'); ?>>Servicio de Deportes</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Prevención y Riesgos Laborales'); ?>>Prevención y Riesgos Laborales</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Consejo de Estudiantes'); ?>>Consejo de Estudiantes</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Sección Sindical CCOO'); ?>>Sección Sindical CCOO</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Seccón Sindical UGT'); ?>>Seccón Sindical UGT</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Sección Sindical CSIF'); ?>>Sección Sindical CSIF</option>
                                                    <option <?php echo form_value('Servicio/Unidad', 'select', 'Otros (Indique en observaciones detalle)'); ?>>Otros (Indique en observaciones detalle)</option>
                                                </select>&nbsp;</b></p>
                                            <p align="center" style="text-align: left"><b>Centro/Departamento:</b>&nbsp;
                                                <select size="1" name="Departamento">
                                                    <option <?php echo form_value('Departamento', 'select', 'Indique su Centro/Departamento'); ?>>Indique su Centro/Departamento</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Facultad de Ciencias Experimentales'); ?>>Facultad de Ciencias Experimentales</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Facultad de Ciencas Sociales y Juridicas'); ?>>Facultad de Ciencas Sociales y Juridicas</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Facultad de Humanidades y Ciencias de la Educación'); ?>>Facultad de Humanidades y Ciencias de la Educación</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Facultad Ciencias de la Salud'); ?>>Facultad Ciencias de la Salud</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Facultad Trabajo Social'); ?>>Facultad Trabajo Social</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Escuela Politécnica Superior'); ?>>Escuela Politécnica Superior</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Escuela Politécnica Superior (Linares)'); ?>>Escuela Politécnica Superior (Linares)</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Centro Andaluz de Arqueología Ibérica'); ?>>Centro Andaluz de Arqueología Ibérica</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Centros de Estudios Avanzados en lenguas Modernas'); ?>>Centros de Estudios Avanzados en lenguas Modernas</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Antropología, Geografía e Historia'); ?>>Antropología, Geografía e Historia</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Biología Animal, Vegetal y Ecología'); ?>>Biología Animal, Vegetal y Ecología</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Biología Experimental'); ?>>Biología Experimental</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ciencias de la Salud'); ?>>Ciencias de la Salud</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Derecho Civil, Derecho Financiero y Tributario'); ?>>Derecho Civil, Derecho Financiero y Tributario</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Derecho Penal, Filosofía del Derecho, Filosofía Moral y Filosofía'); ?>>Derecho Penal, Filosofía del Derecho, Filosofía Moral y Filosofía</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Derecho Público'); ?>>Derecho Público</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Derecho Público y Común Europeo'); ?>>Derecho Público y Común Europeo</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Derecho Público y Derecho Privado Especial'); ?>>Derecho Público y Derecho Privado Especial</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Didáctica de la Expresión Musical, Plástica y Corporal'); ?>>Didáctica de la Expresión Musical, Plástica y Corporal</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Didáctica de las Ciencias'); ?>>Didáctica de las Ciencias</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Economía'); ?>>Economía</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Economía Financiera y Contabilidad'); ?>>Economía Financiera y Contabilidad</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Estadística e Investigación Operativa'); ?>>Estadística e Investigación Operativa</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Filología Española'); ?>>Filología Española</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Filología Inglesa'); ?>>Filología Inglesa</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Enfermería'); ?>>Enfermería</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Física'); ?>>Física</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Geología'); ?>>Geología</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Informática'); ?>>Informática</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería Cartográfica, Geodésica y Fotogrametría'); ?>>Ingeniería Cartográfica, Geodésica y Fotogrametría</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería Eléctrica'); ?>>Ingeniería Eléctrica</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería Electrónica y Automática'); ?>>Ingeniería Electrónica y Automática</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería de Telecomunicación'); ?>>Ingeniería de Telecomunicación</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería Gráfica, Diseño y Proyectos'); ?>>Ingeniería Gráfica, Diseño y Proyectos</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería Mecánica y Minera'); ?>>Ingeniería Mecánica y Minera</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Ingeniería Química, Ambiental y de los Materiales'); ?>>Ingeniería Química, Ambiental y de los Materiales</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Lenguas y Culturas Mediterráneas'); ?>>Lenguas y Culturas Mediterráneas</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Matemáticas'); ?>>Matemáticas</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Org. de Empresas Márketing y Sociología'); ?>>Org. de Empresas Márketing y Sociología</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Patrimonio Histórico'); ?>>Patrimonio Histórico</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Pedagogía'); ?>>Pedagogía</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Psicología'); ?>>Psicología</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Química Física y Analítica'); ?>>Química Física y Analítica</option>
                                                    <option <?php echo form_value('Departamento', 'select', 'Química Inorgánica y Orgánica'); ?>>Química Inorgánica y Orgánica</option>
                                                </select>
                                            </p>
                                            <p><b>Dirección e-mail:</b>&nbsp;
                                                <input type="text" name="email" id="email" size="25"  <?php echo form_value('email'); ?>  value="@ujaen.es"><br>
                                                <SPAN style="color:red; font-size:9px">Por favor, para realizar la solicitud indique su 
                                                    <u>correo electrónico personal de la Universidad de Jaén</u>.</span>
                                            </p>
                                            <p align="center" style="text-align: left"><b>Teléfono: </b>
                                                <input type="text" onkeypress="return soloNumeros(event)" name="telefono" id="telefono" <?php echo form_value('telefono'); ?> size="9">
                                                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Móvil:</b>&nbsp;<input type="text" onkeypress="return soloNumeros(event)" name="movil" id="movil" <?php echo form_value('movil'); ?> size="9"></p>
                                            <p align="center"><font size="3"><b>ESPACIOS Y SERVICIOS SOLICITADOS:</b></font></p>
                                            <b>Espacio solicitado: 
                                                <font size="3">
                                                <input id="espacio2" size ="54" type="text" readonly></font>
                                                <input id="espacio" name="espacio" type="hidden" value=""></font>
                                            </b>
                                            </p>
                                            <p align="center" style="text-align: left"><b>Fecha (s) de celebración:&nbsp;
                                                    <br/>
                                                    <input type="text" name="fecha" id="fecha" size="6" disabled><input type="text" name="hora_inicio" id="hora_inicio" size="1" disabled></b></p>
                                            <p align="center" style="text-align: left"><b>Horario (s) de Finalización Reserva:&nbsp;
                                                    <br/>

                                                    <input type="text" name="fecha2" id="fecha2" size="6" disabled>                    
                                                    <select name="hora_finalizacion" id="hora_finalizacion">
                                                        <option value="08:00" <?php echo form_value('hora_finalizacion', 'select', '08:00'); ?>>08:00</option>
                                                        <option value="08:30" <?php echo form_value('hora_finalizacion', 'select', '08:30'); ?>>08:30</option>
                                                        <option value="09:00" <?php echo form_value('hora_finalizacion', 'select', '09:00'); ?>>09:00</option>
                                                        <option value="09:30" <?php echo form_value('hora_finalizacion', 'select', '09:30'); ?>>09:30</option>
                                                        <option value="10:00" <?php echo form_value('hora_finalizacion', 'select', '10:00'); ?>>10:00</option>
                                                        <option value="10:30" <?php echo form_value('hora_finalizacion', 'select', '10:30'); ?>>10:30</option>
                                                        <option value="11:00" <?php echo form_value('hora_finalizacion', 'select', '11:00'); ?>>11:00</option>
                                                        <option value="11:30" <?php echo form_value('hora_finalizacion', 'select', '11:30'); ?>>11:30</option>
                                                        <option value="12:00" <?php echo form_value('hora_finalizacion', 'select', '12:00'); ?>>12:00</option>
                                                        <option value="12:30" <?php echo form_value('hora_finalizacion', 'select', '12:30'); ?>>12:30</option>
                                                        <option value="13:00" <?php echo form_value('hora_finalizacion', 'select', '13:00'); ?>>13:00</option>
                                                        <option value="13:30" <?php echo form_value('hora_finalizacion', 'select', '13:30'); ?>>13:30</option>
                                                        <option value="14:00" <?php echo form_value('hora_finalizacion', 'select', '14:00'); ?>>14:00</option>
                                                        <option value="14:30" <?php echo form_value('hora_finalizacion', 'select', '14:30'); ?>>14:30</option>
                                                        <option value="15:00" <?php echo form_value('hora_finalizacion', 'select', '15:00'); ?>>15:00</option>
                                                        <option value="15:30" <?php echo form_value('hora_finalizacion', 'select', '15:30'); ?>>15:30</option>
                                                        <option value="16:00" <?php echo form_value('hora_finalizacion', 'select', '16:00'); ?>>16:00</option>
                                                        <option value="16:30" <?php echo form_value('hora_finalizacion', 'select', '16:30'); ?>>16:30</option>
                                                        <option value="17:00" <?php echo form_value('hora_finalizacion', 'select', '17:00'); ?>>17:00</option>
                                                        <option value="17:30" <?php echo form_value('hora_finalizacion', 'select', '17:30'); ?>>17:30</option>
                                                        <option value="18:00" <?php echo form_value('hora_finalizacion', 'select', '18:00'); ?>>18:00</option>
                                                        <option value="18:30" <?php echo form_value('hora_finalizacion', 'select', '18:30'); ?>>18:30</option>
                                                        <option value="19:00" <?php echo form_value('hora_finalizacion', 'select', '19:00'); ?>>19:00</option>
                                                        <option value="19:30" <?php echo form_value('hora_finalizacion', 'select', '19:30'); ?>>19:30</option>
                                                        <option value="20:00" <?php echo form_value('hora_finalizacion', 'select', '20:00'); ?>>20:00</option>
                                                        <option value="20:30" <?php echo form_value('hora_finalizacion', 'select', '20:30'); ?>>20:30</option>
                                                        <option value="21:00" <?php echo form_value('hora_finalizacion', 'select', '21:00'); ?>>21:00</option>
                                                        <option value="21:30" <?php echo form_value('hora_finalizacion', 'select', '21:30'); ?>>21:30</option>
                                                        <option value="22:00" <?php echo form_value('hora_finalizacion', 'select', '22:00'); ?>>22:00</option>
                                                    </select>
                                                    <p align="center" style="text-align: left"><b>Horario (s) de Inicio Acto:&nbsp;
                                                            <br/>
                                                            <select name="hora_acto" id="hora_acto" onChange="hora_acto_validar(this.selectedIndex)">
                                                                <option value="08:00" <?php echo form_value('hora_acto', 'select', '08:00'); ?>>08:00</option>
                                                                <option value="08:30" <?php echo form_value('hora_acto', 'select', '08:30'); ?>>08:30</option>
                                                                <option value="09:00" <?php echo form_value('hora_acto', 'select', '09:00'); ?>>09:00</option>
                                                                <option value="09:30" <?php echo form_value('hora_acto', 'select', '09:30'); ?>>09:30</option>
                                                                <option value="10:00" <?php echo form_value('hora_acto', 'select', '10:00'); ?>>10:00</option>
                                                                <option value="10:30" <?php echo form_value('hora_acto', 'select', '10:30'); ?>>10:30</option>
                                                                <option value="11:00" <?php echo form_value('hora_acto', 'select', '11:00'); ?>>11:00</option>
                                                                <option value="11:30" <?php echo form_value('hora_acto', 'select', '11:30'); ?>>11:30</option>
                                                                <option value="12:00" <?php echo form_value('hora_acto', 'select', '12:00'); ?>>12:00</option>
                                                                <option value="12:30" <?php echo form_value('hora_acto', 'select', '12:30'); ?>>12:30</option>
                                                                <option value="13:00" <?php echo form_value('hora_acto', 'select', '13:00'); ?>>13:00</option>
                                                                <option value="13:30" <?php echo form_value('hora_acto', 'select', '13:30'); ?>>13:30</option>
                                                                <option value="14:00" <?php echo form_value('hora_acto', 'select', '14:00'); ?>>14:00</option>
                                                                <option value="14:30" <?php echo form_value('hora_acto', 'select', '14:30'); ?>>14:30</option>
                                                                <option value="15:00" <?php echo form_value('hora_acto', 'select', '15:00'); ?>>15:00</option>
                                                                <option value="15:30" <?php echo form_value('hora_acto', 'select', '15:30'); ?>>15:30</option>
                                                                <option value="16:00" <?php echo form_value('hora_acto', 'select', '16:00'); ?>>16:00</option>
                                                                <option value="16:30" <?php echo form_value('hora_acto', 'select', '16:30'); ?>>16:30</option>
                                                                <option value="17:00" <?php echo form_value('hora_acto', 'select', '17:00'); ?>>17:00</option>
                                                                <option value="17:30" <?php echo form_value('hora_acto', 'select', '17:30'); ?>>17:30</option>
                                                                <option value="18:00" <?php echo form_value('hora_acto', 'select', '18:00'); ?>>18:00</option>
                                                                <option value="18:30" <?php echo form_value('hora_acto', 'select', '18:30'); ?>>18:30</option>
                                                                <option value="19:00" <?php echo form_value('hora_acto', 'select', '19:00'); ?>>19:00</option>
                                                                <option value="19:30" <?php echo form_value('hora_acto', 'select', '19:30'); ?>>19:30</option>
                                                                <option value="20:00" <?php echo form_value('hora_acto', 'select', '20:00'); ?>>20:00</option>
                                                                <option value="20:30" <?php echo form_value('hora_acto', 'select', '20:30'); ?>>20:30</option>
                                                                <option value="21:00" <?php echo form_value('hora_acto', 'select', '21:00'); ?>>21:00</option>
                                                                <option value="21:30" <?php echo form_value('hora_acto', 'select', '21:30'); ?>>21:30</option>
                                                                <option value="22:00" <?php echo form_value('hora_acto', 'select', '22:00'); ?>>22:00</option>
                                                            </select>
                                                        </b>
                                                    </p>
                                                    <b>Evento a celebrar:</b>
                                                    <br/>
                                                    <textarea rows="3" name="evento" id="evento" cols="103"><?php echo form_value('evento', 'textarea'); ?></textarea>


                                                    <DIV id="d_capacidad" style="text-align:center"></DIV>
                                                    <DIV id="d_normas" style="text-align:center"></DIV>


                                                    <DIV id="d_txt"><font size="2">Necesidades para la celebración del evento</font>:</DIV>
                                                    <b>
                                                        <DIV id="d_sin_necesidades">
                                                            <input type="checkbox" name="sin_necesidades" onclick="Sin_Nece_Click()" <?php echo form_value('sin_necesidades', 'checkbox'); ?>>Sin necesidades
                                                        </DIV>
                                                        <DIV id="d_num_personas_mesa_presidencial" style="display: none">Número de personas en la mesa presidencial&nbsp; 
                                                            &nbsp;<input type="text" onkeypress="return soloNumeros(event)" name="num_personas_mesa_presidencial" id="num_personas_mesa_presidencial" maxlength="2" <?php echo form_value('num_personas_mesa_presidencial'); ?> onchange="Desactiva_Sin_Nece()" size="2">
                                                        </DIV>
                                                        <DIV id="d_videoconferencia">
                                                            <input type="checkbox" name="videoconferencia" onclick="Desactiva_Sin_Nece()" <?php echo form_value('sin_necesidades', 'checkbox'); ?>>
                                                            <span style=color:red;>Videoconferencia&nbsp;</span>
                                                            <span style=color:red;font-size:11px;> (Le recordamos que al seleccionar esta casilla se iniciará el proceso para la puesta en funcionamiento de este recurso por la Unidad de Medios Audiovisuales)
                                                            </span>
                                                        </DIV>
                                                        <DIV id="d_traduccion_simultanea">
                                                            <input type="checkbox" name="traduccion_simultanea" onclick="Desactiva_Sin_Nece()" <?php echo form_value('sin_necesidades', 'checkbox'); ?>>
                                                            <span style=color:red;>Traducción Simultánea&nbsp;</span>
                                                            <span style=color:red;></span>
                                                            <span style=color:red;font-size:11px;> (Le recordamos que al seleccionar esta casilla se iniciará el proceso para la puesta en funcionamiento de este recurso por la Unidad de Medios Audiovisuales)</span>
                                                        </DIV>
                                                        <DIV id="d_canon">
                                                            <input type="checkbox" name="canon" onclick="Desactiva_Sin_Nece()" <?php echo form_value('canon', 'checkbox'); ?>>Cañón
                                                        </DIV>
                                                        <DIV id="d_CPU">
                                                            <input type="checkbox" name="CPU" onclick="Desactiva_Sin_Nece()" <?php echo form_value('CPU', 'checkbox'); ?>>CPU
                                                        </DIV>
                                                        <DIV id="d_megafonia">
                                                            <input type="checkbox" name="megafonia" onclick="Desactiva_Sin_Nece()" <?php echo form_value('megafonia', 'checkbox'); ?>>Megafonía
                                                        </DIV>
                                                        <DIV id="d_megafonia_debate" style="display: none">
                                                            <input type="checkbox" name="megafonia_debate" onclick="Desactiva_Sin_Nece()" <?php echo form_value('megafonia_debate', 'checkbox'); ?>>Megafonía de debate
                                                        </DIV>
                                                        <DIV id="d_audio_pc">
                                                            <input type="checkbox" name="audio_pc" onclick="Desactiva_Sin_Nece()" <?php echo form_value('audio_pc', 'checkbox'); ?>>Audio PC
                                                        </DIV>
                                                        <DIV id="d_cable_red">
                                                            <input type="checkbox" name="cable_red" onclick="Desactiva_Sin_Nece()" <?php echo form_value('cable_red', 'checkbox'); ?>>Cable de red
                                                        </DIV>
                                                        <DIV id="d_cable_portatil">
                                                            <input type="checkbox" name="cable_portatil" onclick="Desactiva_Sin_Nece()" <?php echo form_value('cable_portatil', 'checkbox'); ?>>Cable para portátil
                                                        </DIV>
                                                        <DIV id="d_altavoces">
                                                            <input type="checkbox" name="altavoces" onclick="Desactiva_Sin_Nece()" <?php echo form_value('altavoces', 'checkbox'); ?>>Altavoces
                                                        </DIV>
                                                        <DIV id="d_micro_inalambrico">
                                                            <input type="checkbox" name="micro_inalambrico" onclick="Desactiva_Sin_Nece()" <?php echo form_value('micro_inalambrico', 'checkbox'); ?>>Micro inalámbrico
                                                        </DIV>
                                                        <DIV id="d_atril">
                                                            <input type="checkbox" name="atril" onclick="Desactiva_Sin_Nece()" <?php echo form_value('atril', 'checkbox'); ?>>Atril
                                                        </DIV>
                                                        <DIV id="d_tv">
                                                            <input type="checkbox" name="tv" onclick="Desactiva_Sin_Nece()" <?php echo form_value('tv', 'checkbox'); ?>>TV
                                                        </DIV>
                                                        <DIV id="d_conexion_cuadro_electrico">
                                                            <input type="checkbox" name="conexion_cuadro_electrico" onclick="Desactiva_Sin_Nece()" <?php echo form_value('conexion_cuadro_electrico', 'checkbox'); ?>>Conexión cuadro eléctrico
                                                        </DIV>
                                                        <DIV id="d_agua_caliente_camerinos">
                                                            <input type="checkbox" name="agua_caliente_camerinos" onclick="Desactiva_Sin_Nece()" <?php echo form_value('agua_caliente_camerinos', 'checkbox'); ?>>Agua caliente camerinos
                                                        </DIV>
                                                        <DIV id="d_botellas">
                                                            Agua&nbsp; nº de botellas&nbsp;&nbsp;
                                                            <input type="text" onkeypress="return soloNumeros(event)" name="botellas" maxlength="2" id="botellas" onchange="Desactiva_Sin_Nece()" size="2" <?php echo form_value('botellas'); ?>>
                                                        </DIV>
                                                    </b>

                                                    <b>Observaciones:</b>
                                                    <br/>
                                                    <textarea rows="3" name="observaciones" cols="106"><?php echo form_value('observaciones', 'textarea'); ?></textarea>
                                                    <p style="text-align: center">
                                                        <input type="submit" value="Enviar" name="Enviar" onclick="return Valida()"><input type="reset" value="Restablecer" name="B2"></p>
                                            </p>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<?php endif; ?>
    </body>
</html>

<SCRIPT type='text/javascript'>

    function Valida() {

        var oblig = "";
        if ((document.getElementById('solicitante').value === '') || (document.getElementById('solicitante').value.length <= 12)) {
            oblig = oblig + ", Nombre y Apellidos";
        }
        if (document.getElementById('edificio').selectedIndex === 0) {
            oblig = oblig + ", Edificio";
        }
        if (document.getElementById('dependencia').value === '') {
            oblig = oblig + ", Dependencia";
        }
        if (document.getElementById('sector').selectedIndex === 0) {
            oblig = oblig + ", Sector";
        }
        if (document.getElementById('email').value === '') {
            oblig = oblig + ", E-mail";
        }
        if ((document.getElementById('telefono').value === '') || (document.getElementById('telefono').value.length <= 3)) {
            oblig = oblig + ", Teléfono";
        }
        if ((document.getElementById('evento').value === '') || (document.getElementById('evento').value.length <= 4)) {
            oblig = oblig + ", Evento";
        }
        if (document.getElementById('fecha').value === '') {
            oblig = oblig + ", Fecha";
        }
        if (document.getElementById('hora_inicio').value === '') {
            oblig = oblig + ", Hora de inicio reserva";
        }
        if (document.getElementById('hora_finalizacion').value === '') {
            oblig = oblig + ", Hora de finalización reserva";
        }
        if (document.getElementById('hora_acto').value === '') {
            oblig = oblig + ", Hora de inicio del acto";
        }

        if (oblig !== '') {
            alert("Para continuar debe cumplimentar correctamente los siguientes campos:\n\n" + oblig.substring(2) + ".");
            return false;
        }

        var filtro = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if (!filtro.test(document.getElementById('email').value)) {
            alert("La dirección E-mail introducida es incorrecta.");
            document.getElementById('email').focus();
            return (false);
        }


        return Comprueba_nece();
    }

    function soloNumeros(evt)
    {
        var keyPressed = (evt.which) ? evt.which : event.keyCode;
        return !(keyPressed > 31 && (keyPressed < 48 || keyPressed > 57));
    }

    function Comprueba_nece() {
        //espacios con necesidades

        if (arrEquip[curData[0]] && arrEquip[curData[0]] !== "000000000000000000") {
            Nece = false;
            for (i = 0; i <= 15; i++) {
                if (FrontPage_Form1.elements[arrCheck[i]].checked === true) {
                    Nece = true;
                    break;
                }
            }
            if (Nece === false) {
                if (confirm("No ha seleccionado ninguna de las casillas de 'Necesidades para la celebración del evento'.\n\nEn cualquier caso, ¿ desea Vd. continuar con la solicitud de esta reserva ?"))
                {
                    FrontPage_Form1.elements["sin_necesidades"].checked = true;
                }
                else {
                    return false;
                }
            }
        }

        return true;
    }

    function Sin_Nece_Click() {
        if (FrontPage_Form1.sin_necesidades.checked === true) {
            for (i = 1; i <= 17; i++) {
                if (i >= 16) {
                    FrontPage_Form1.elements[arrCheck[i]].value = '';
                }
                else {
                    FrontPage_Form1.elements[arrCheck[i]].checked = false;
                }
            }
        }
    }

    function Desactiva_Sin_Nece() {
        FrontPage_Form1.sin_necesidades.checked = false;
    }

    function Controla_Checks() {
        var nombrediv = '';
        for (i = 0; i <= 17; i++) {
            nombrediv = 'd_' + arrCheck[i];
            document.getElementById(nombrediv).style.display = '';
        }
        document.getElementById('d_txt').style.display = '';
        document.getElementById('d_normas').style.display = 'none';
        if (arrNames[curData[0]])
        {

            var Comp = arrEquip[curData[0]];
            for (i = 0; i <= 17; i++) {
                if (Comp.substring(i, i + 1) === "0")
                {
                    nombrediv = 'd_' + arrCheck[i];
                    document.getElementById(nombrediv).style.display = 'none';
                }
            }
            if (Comp === '000000000000000000') {
                document.getElementById('d_txt').style.display = 'none';
            }
            if (arrCapa[curData[0]] !== "") {
                document.getElementById("d_capacidad").innerHTML = "<b><font color='#FF0000'>Capacidad de la sala: " + arrCapa[curData[0]] + "</font></b>";
            }
            else {
                document.getElementById("d_capacidad").innerHTML = "";
            }
            if (arrNormas[curData[0]] !== "") {
                document.getElementById("d_normas").style.display = '';
                document.getElementById("d_normas").innerHTML = arrNormas[curData[0]];
            }
            else {
                document.getElementById("d_normas").style.display = 'none';
            }
        }
        else {

            document.getElementById("d_capacidad").innerHTML = "";
            document.getElementById('d_txt').style.display = 'none';
            document.getElementById('d_normas').style.display = 'none';
            for (i = 0; i <= 17; i++) {
                nombrediv = 'd_' + arrCheck[i];
                document.getElementById(nombrediv).style.display = 'none';
            }
        }
    }
</SCRIPT>

<script>

    function logout() {
        $.get("logout.php");
        parent.window.location.reload();
        return false;
    }
    function parse() {

        var result = [],
                tmp = [];
        location.search
                .substr(1)
                .split("&")
                .forEach(function (item) {
                    tmp = item.split("=");
                    result.push(decodeURIComponent(tmp[1]));
                });
        return result;
    }




    var curData = parse();
    $("#titleBook").html("Reserva - " + curData[1]);
    document.getElementById('site').value = curData[1];
    Controla_Checks();
    var bookDate = curData[2].split(" ")[0];
    var bookTime = curData[2].split(" ")[1];
    bookTime = bookTime.substring(0, bookTime.lastIndexOf(":"));

    document.getElementById('fecha').value = document.getElementById('fecha2').value = bookDate;
    document.getElementById('hora_inicio').value = bookTime;
    document.getElementById('espacio2').value = arrNames[curData[0]];
    document.getElementById('espacio').value = arrNames[curData[0]];
    document.getElementById('startdate').value = curData[2];

    document.getElementById('fecha').defaultValue = document.getElementById('fecha2').defaultValue = bookDate;
    document.getElementById('hora_inicio').defaultValue = bookTime;
    document.getElementById('espacio2').defaultValue = arrNames[curData[0]];
    document.getElementById('espacio').defaultValue = arrNames[curData[0]];
    document.getElementById('startdate').defaultValue = curData[2];

    var x = document.getElementById('hora_finalizacion');
    var y = document.getElementById('hora_acto');
    var startDel = false;

    for (var i = x.length - 1; i >= 0; i--) {
        if (!startDel && x[i].value === bookTime)
            startDel = true;
        else
        if (startDel) {
            x.remove(i);
            y.remove(i);
        }
    }
    x.remove(0);


<?php
require './mysqlData.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$var1 = filter_input(INPUT_GET, 'date');
$var2 = filter_input(INPUT_GET, 'site');
$var3 = filter_input(INPUT_GET, 'node');
$var4 = strtotime('+1 day', strtotime($var1));
$var4 = date('Y-m-d H:i:s', $var4);
$var4 = explode("-", explode(" ", $var4)[0]);
$var4 = mktime(0, 0, 0, $var4[1], $var4[2], $var4[0]);

$sql = "SELECT from_unixtime(MIN(startdate),\"%H:%i\") startdate FROM reserve_table WHERE plane = '$var2' AND node = $var3 AND (startdate BETWEEN UNIX_TIMESTAMP('$var1') AND $var4) AND (enddate BETWEEN UNIX_TIMESTAMP('$var1') AND $var4);";

$result = $conn->query($sql);
$return;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $return = $row;
        echo "var curReser = \"" . $return['startdate'] . "\";";
    }
}
?>

    for (var j = 0; j < x.length; j++) {
        if (x[j].value === curReser)
            var s = j;
    }

    for (var m = x.length; m > s + 1; m--) {
        x.remove(m);
        y.remove(m);
    }
    x.remove(m);

    function hora_acto_validar(v) {
        if (v > x.selectedIndex) {
            alert("La hora de inicio no puede estar fuera del horario de reserva.");
            y.options[0].selected = 'selected';
        }
    }


</script>