<?php
/* * * begin the session ** */
session_start();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (!isset($_SESSION['user_id'])) {
    $message = 'You must be logged in to access this page';
    
    $valid = false;
} else {
    try {
        /*         * * connect to database ** */
        /*         * * mysql hostname ** */
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

        /*         * * if we have no something is wrong ** */
        $valid = false;
        if ($phpro_username == false) {
            $message = 'Access Error';
        } else {
            $message = 'Welcome ' . $phpro_username;
            $valid = true;
        }
    } catch (Exception $e) {
        /*         * * if we are here, something is wrong in the database ** */
        $message = 'We are unable to process your request. Please try again later"';
    }

    if ($valid && ($_SERVER["REQUEST_METHOD"] == "POST")) {

        $arrayPost = array();

        require 'phpdata.php';

        $id = $nombre = $edificio = $dependencia = $sector = $servicio = $departamento = $email = $telefono = $movil = "";
        $espacio = $node = $startdate = $enddate = $eventdate = $event = $observaciones = $num_per_mesa_presi = $botellas = $site = "";

        $error = "";

        if (empty($_POST["id"])) {
            $error = $error . ", Error catastrófico";
        } else {
            $arrayPost['id'] = test_input($_POST["id"]);
        }



        if (empty($_POST["solicitante"])) {
            $error = $error . ", El nombre es necesario";
        } else {
            $arrayPost['solicitante'] = test_input($_POST["solicitante"]);
            if (!preg_match("/^[a-z .\-áéíóúÁÉÍÓÚ]+$/i", $arrayPost['solicitante'])) {
                $error = $error . ", Solo se admiten letras y espacios en el nombre";
            }
            if (strlen($nombre) > 70) {
                $error = $error . ", El nombre es demasiado largo";
            }
        }

        

        if ($_POST["edificio"] === "-") {
            $error = $error . ", El campo edificio es obligatorio";
        } else {
            $arrayPost['edificio'] = test_input($_POST["edificio"]);
            if (!in_array($arrayPost['edificio'], $arrayEdificio)) {
                $error = $error . ", El edificio seleccionado no existe";
            }
        }

        

        if (empty($_POST["dependencia"])) {
            $error = $error . ", El campo dependencia es obligatorio";
        } else {
            $arrayPost['dependencia'] = test_input($_POST["dependencia"]);
            if (!preg_match("/[0-9][0-9][0-9]/", $arrayPost['dependencia'])) {
                $error = $error . ", La dependencia es incorrecta";
            }
        }

        

        if ($_POST["sector"] === "-") {
            $error = $error . ", El campo sector es obligatorio";
        } else {
            $arrayPost['sector'] = test_input($_POST["sector"]);
            if (!in_array($arrayPost['sector'], $arraySector)) {
                $error = $error . ", El sector seleccionado no existe";
            }
        }

        

        if ($_POST["Servicio/Unidad"] === "Indique Órgano de Gobierno/Servicio") {
            $arrayPost['Servicio/Unidad'] = "Sin especificar";
        } else {
            $arrayPost['Servicio/Unidad'] = test_input($_POST["Servicio/Unidad"]);
        }

        

        if ($_POST["Departamento"] === "Indique su Centro/Departamento") {
            $arrayPost['Departamento'] = "Sin especificar";
        } else {
            $arrayPost['Departamento'] = test_input($_POST["Departamento"]);
        }

        

        if ((empty($_POST["email"])) || test_input($_POST["email"]) === "@ujaen.es") {
            $error = $error . ", Es necesario introducir el email";
        } else {
            $arrayPost['email'] = test_input($_POST["email"]);
            if (!filter_var($arrayPost['email'], FILTER_VALIDATE_EMAIL)) {
                $error = $error . ", Formato de email incorrecto";
            }
            if (!strpos($arrayPost['email'], "ujaen.es")) {
                $error = $error . ", El correo facilitado no es de la Ujaen";
            }
        }

        

        if (empty($_POST["telefono"])) {
            $error = $error . ", Es necesario introducir un teléfono";
        } else {
            $arrayPost['telefono'] = test_input($_POST["telefono"]);
            if (!preg_match("/[+]?[0-9]{7,15}/", $arrayPost['telefono'])) {
                $error = $error . ", Introduzca un número de teléfono correcto";
            }
        }

        

        if (empty($_POST["movil"])) {
            $arrayPost['movil'] = "Sin especificar";
        } else {
            $arrayPost['movil'] = test_input($_POST["movil"]);
        }

        
        

        if (empty($_POST["Hora_de_inicio_del_acto"])) {
            $error = $error . ", error con la hora del acto";
        } else {
            $arrayPost['Hora de inicio del acto'] = test_input($_POST["Hora_de_inicio_del_acto"]);
        }

        if (empty($_POST["evento"])) {
            $error = $error . ", Es necesario introducir una descripción del evento a realizar";
        } else {
            $arrayPost['evento'] = test_input($_POST["evento"]);
        }

        

        if (empty($_POST["observaciones"])) {
            $arrayPost['observaciones'] = "Sin especificar";
        } else {
            $arrayPost['observaciones'] = test_input($_POST["observaciones"]);
        }

        if (empty($_POST["notificacion"])) {
            $arrayPost['notificacion'] = "Sin especificar";
        } else {
            $arrayPost['notificacion'] = test_input($_POST["notificacion"]);
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

        $arrayPost['result'] = $result;




        $arrayPost['startdate'] = test_input($_POST['startdate']);
        $arrayPost['enddate'] = test_input($_POST['enddate']);
        $arrayPost['name'] = test_input($_POST['name']);
        $arrayPost['plane'] = test_input($_POST['plane']);
        $arrayPost['Observaciones'] = test_input($_POST['Observaciones']);
        


        if ($error === "") {
            if ($_POST['action'] === 'reject') {      
                require "reject.php";
            }

            if ($_POST['action'] === 'accept') {
                require "accept.php";
            }
        } else {
            echo "<script>console.log(\"$error\");</script>";
        }
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
                            <li class="active"><a href="validate.php">Solicitudes</a></li>
                            <li><a href="acepted.php">Aceptadas</a></li>
                            <li><a href="record.php">Historial</a></li>
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


        <div class="container-fluid">
            <div class="row">

                <div id="blank" class="col-lg-1 col-md-1">

                </div>

                <div id="selector" class="col-lg-10 col-md-10">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (isset($output)): ?>    
                                <div id="popup" class="alert alert-dismissible alert-info">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <?php echo $output; ?>
                                </div>
                            <?php endif; ?>


                            <?php if ($valid): ?>                    
                                <?php
                                require './phpdata.php';
                                $id = filter_input(INPUT_GET, 'id');

                                if ($id !== null) {
                                    require './mysqlData.php';

                                    // Create connection
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    // Check connection
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }


                                    $sql = "SELECT * FROM reserve_table WHERE id=$id;";

                                    
                                    $result = $conn->query($sql);

                                    $header = "";


                                    if ($result->num_rows === 1) {
                                        $row = $result->fetch_assoc();

                                       
                                        $userinfo = json_decode($row['userinfo'], true);
                                        $eventinfo = json_decode($row['eventinfo'], true);
                                        $necesities = $row['necesities'];
                                    } else {
                                        echo "<h2>Hay un error con la reserva seleccionada</h2>";
                                    }
                                } else {
                                    require './mysqlData.php';

                                    // Create connection
                                    $conn = new mysqli($servername, $username, $password, $dbname);
                                    // Check connection
                                    if ($conn->connect_error) {
                                        die("Connection failed: " . $conn->connect_error);
                                    }


                                    $sql = "SELECT id, plane, name, node, startdate, enddate, userinfo FROM reserve_table WHERE state='Revising' AND enddate > UNIX_TIMESTAMP(NOW()) ORDER BY node ASC, startdate ASC;";

                                    
                                    $result = $conn->query($sql);
                                    $return;

                                    $header = "";


                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $return = $row;
                                            if ($row['node'] !== $header) {
                                                if ($header !== "") {
                                                    echo "</table></div>";
                                                }
                                                $header = $row['node'];
                                                echo "<h2>" . $row['name'] . "</h2>";
                                                echo "<div class=\"reserveTable\">";
                                                echo "<table>";
                                                echo "<tr>";
                                                echo "<td>Espacios</td>";
                                                
                                                echo "<td>Fecha inicio</td>";
                                                echo "<td>Fecha fin</td>";
                                                echo "<td>Solicitante</td></tr>";
                                            }
                                            $userinfo = json_decode($row['userinfo'], true);
                                            echo "<tr class=\"reserveTR\" onclick=\"window.location.href = '/validate.php?id=" . $row['id'] . "';\">";
                                            echo "<td>";
                                            echo $row['plane'];
                                            echo "</td>";
                                            echo "<td>";
                                            echo date('Y/m/d H:i', $row['startdate']);
                                            echo "</td>";
                                            echo "<td>";
                                            echo date('Y/m/d H:i', $row['enddate']);
                                            echo "</td>";
                                            echo "<td style=\" width : 30%;\">";
                                            echo $userinfo['Nombre y apellidos'];
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                        echo "</tr></table></div>";
                                    } else
                                        {

                                    echo "<h2>No hay nuevas reservas por validar</h2>";
                                }
                                }
                                ?>

    <?php if ($id !== null): ?>

                                    <div class="panel panel-primary">
                                        <div class="panel-heading">
                                            <b>Solicitud</b>
                                        </div>
                                        <div class="list-group-item">
                                            <table style="width:100%">
                                                <tr>
                                                    <td>
        <?php echo "Instalación: <b>" . $row['name'] . ", " . $row['plane'] . "</b>"; ?>
                                                    </td>
                                                    <td class="right">
        <?php echo "Fecha: <b>" . date("Y-m-d", $row['startdate']) . "</b> de <b>" . date("H:i", $row['startdate']) . "</b> a <b>" . date("H:i", $row['enddate']) . "</b>" ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>    

                                        <form id="reserveForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                            <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                                            <input type="hidden" name="startdate" value="<?php echo $row['startdate'] ?>">
                                            <input type="hidden" name="enddate" value="<?php echo $row['enddate'] ?>">
                                            <input type="hidden" name="name" value="<?php echo $row['name'] ?>">
                                            <input type="hidden" name="plane" value="<?php echo $row['plane'] ?>">
                                            <input type="hidden" name="Hora de inicio del acto" value="<?php echo $eventinfo ['Hora de inicio del acto'] ?>">

                                            <div class="list-group-item">
                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                            <div id=" inner">
                                                                <label>
        <?php echo "Solicitante: "; ?>
                                                                </label>
                                                                <span>
                                                                    <input  class="inputBold inputfillspace" type = "text" name = "solicitante" disabled value="<?php echo $userinfo['Nombre y apellidos']; ?>">
                                                                </span>   
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>


                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                            <div id="inner">
                                                                <label>
        <?php echo "Edificio: " ?>
                                                                </label>
                                                                <span>
                                                                    <input class="inputBold inputfillspace" type = "text" name = "edificio" disabled value = "<?php echo $userinfo['Edificio'] ?>" >
                                                                </span>
                                                        </td>
                                                        <td>
                                                            <div id="inner">
                                                                <label>
        <?php echo "Dependencia: " ?>
                                                                </label>
                                                                <span>
                                                                    <input class="inputBold inputfillspace" type = "text" name = "dependencia" disabled value = "<?php echo $userinfo['Dependencia'] ?>"  >
                                                                </span>
                                                        </td>
                                                        <td>
                                                            <div id="inner">
                                                                <label>
        <?php echo "Sector: " ?>
                                                                </label>
                                                                <span>
                                                                    <input class="inputBold inputfillspace" type = "text" name = "sector" disabled value = "<?php echo $userinfo['Sector'] ?>" >
                                                                </span>
                                                        </td>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class=" tableValidate" >
                                                    <tr>
                                                        <td>
                                                            <div id=" inner">
                                                                <label> 
        <?php echo "Servicio: " ?> 
                                                                </label>
                                                                <span>
                                                                    <input   class=" inputBold inputfillspace"  type = "text"  name = "Servicio/Unidad"  disabled value = "<?php echo $userinfo['Servicio'] ?>"    >
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class=" tableValidate" >
                                                    <tr>
                                                        <td>
                                                            <div id=" inner">
                                                                <label> 
        <?php echo "Centro/Departamento: " ?> 
                                                                </label>
                                                                <span>
                                                                    <input class=" inputBold inputfillspace"  type = "text"  name = "Departamento"  disabled value = "<?php echo $userinfo['Centro/Departamento'] ?>"    >
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class=" tableValidate" >
                                                    <tr>
                                                        <td>
                                                            <div id=" inner">
                                                                <label> 
        <?php echo "Email: " ?> 
                                                                </label>
                                                                <span>
                                                                    <input class=" inputBold inputfillspace"  type = "text"  name = "email"  disabled value = "<?php echo $userinfo['E-mail'] ?>"    >
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class=" tableValidate" >
                                                    <tr>
                                                        <td>
                                                            <div id=" inner">
                                                                <label> 
        <?php echo "Teléfono: " ?> 
                                                                </label>
                                                                <span>
                                                                    <input class=" inputBold inputfillspace"  type = "text"  name = "telefono"  disabled value = "<?php echo $userinfo['Teléfono'] ?>"    >
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div id=" inner">
                                                                <label> 
        <?php echo "Móvil: " ?> 
                                                                </label>
                                                                <span>
                                                                    <input class=" inputBold inputfillspace"  type = "text"  name = "movil"  disabled value = "<?php echo $userinfo['Móvil'] ?>"    >
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="list-group-item">

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                            El evento empezará a las: <b><?php echo date("H:i", $eventinfo ['Hora de inicio del acto']) ?></b>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
        <?php echo "Evento&nbspa&nbspcelebrar: " ?>
                                                        </td>
                                                    </tr>
                                                </table>



                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                            <textarea class="inputBold" rows="3" name="evento" id="Evento a celebrar" disabled><?php echo $eventinfo ['Evento a celebrar'] ?></textarea>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
        <?php echo "Observaciones: " ?>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                            <textarea class="inputBold" rows="3" name="Observaciones" id="Observaciones" disabled ><?php echo $eventinfo ['Observaciones'] ?></textarea>
                                                        </td>
                                                    </tr>
                                                </table>

                                            </div>

        <?php if ($necesities[0] !== "1"): ?>

                                                <div class="list-group-item">
                                                    <table class="tableValidate">
                                                        <tr>
                                                            <td>
            <?php echo "Necesidades: " ?> 
                                                            </td>
                                                        </tr>
                                                    </table>

                                                    <?php
                                                    $necesities = explode(" ", $necesities);
                                                    $arrCheckFullName = ["sin_necesidades", "Cañon", "CPU", "Megafonía", "Megafonía para debaje", "Audio PC", "Cable red", "Cable portátil", "Altavoces", "Micro inalámbrico", "Atril", "TV", "Videoconferencia", "Traducción Simultánea", "Conexión cuadro eléctrico", "Agua caliente camerinos", "Botellas de agua", "Número personas en mesa presidencial"];
                                                    $arrCheckEsp = ["Botellas de agua", "Número personas en mesa presidencial"];
                                                    $arrEquip = ["111101110110010011", "111101111000100010", "000000000000000000", "111101110000000010", "110000000000000010", "111101110110001111", "111001110000000010", "111001111010000010", "111001110000000010", "111001111000000010", "111001110000000010", "111101110110000011", "111011110000000010", "000000000000000000"];

                                                    $equip = $arrEquip[$row['node']];
                                                    echo "<table class=\"tableValidate\"><tr>";
                                                    $x = 0;
                                                    for ($i = 1; $i < strlen($necesities[0]); $i++) {
                                                        

                                                        if ($x % 3 === 0 && $x !== 0) {
                                                            echo "</tr><tr>";
                                                        }

                                                        if ($necesities[0][$i] === "1") {
                                                            
                                                            echo "<td>"
                                                            . "<input type=\"checkbox\" name=\"" . $arrCheck[$i] . "\" value=\"" . $arrCheck[$i] . "\" checked disabled><b> " . $arrCheckFullName[$i] . "</b>"
                                                            . "</td>";
                                                            $x++;
                                                            
                                                        } else {
                                                            if ($equip[$i] === "1") {
                                                                
                                                                echo "<td>"
                                                                . "<input type=\"checkbox\" name=\"" . $arrCheck[$i] . "\" value=\"" . $arrCheck[$i] . "\" disabled><b> " . $arrCheckFullName[$i] . "</b>"
                                                                . "</td>";
                                                                $x++;
                                                            }
                                                        }
                                                    }
                                                    echo "</tr></table>";


                                                    for ($i = 1; $i < sizeof($necesities); $i++) {
                                                        if ((int) $necesities[$i] > 0) {
                                                            echo "<table class=\"tableValidate\">"
                                                            . "<tr><td><b>"
                                                            . $arrCheckEsp[$i - 1] . ": " . $necesities[$i]
                                                            . "</b></td></tr>"
                                                            . "</table>";
                                                        }
                                                    }
                                                    ?>
                                                </div>


        <?php endif; ?>

                                            <div class="list-group-item">

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
        <?php echo "Notificación al solicitante:" ?>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                            <textarea rows="3" name="notificacion" id="notificacion" ></textarea>
                                                        </td>
                                                    </tr>
                                                </table>


                                            </div>

                                            <div class="list-group-item">

                                                <table class="tableValidate">
                                                    <tr>
                                                        <td>
                                                           <input type='button' id="reject" value='Rechazar petición'>

                                                        </td>
                                                        <td align="right">
                                                            <input type='button' onclick="enableEdit(this)" value='Editar'>
                                                            <input type='button' id="accept" value='Aceptar petición'>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </form>
                                    </div>

                                <?php endif; ?>
                            <?php else: ?>
                                <h2>You should not be here</h2>
<?php endif; ?>
                        </div>
                    </div>         
                </div>
                </body>
                </html>

                <script>
                    function logout() {
                        $.get("logout.php");
                        parent.window.location.assign("/");
                        return false;
                    }

                    function clear() {
                        var nodeList = document.getElementsByTagName("input");
                        var nodeListTextarea = document.getElementsByTagName("textarea");
                        for (var j = 0; j < nodeList.length; j++) {
                            nodeList[j].disabled = false;
                        }
                        for (var j = 0; j < nodeListTextarea.length - 1; j++) {
                            nodeListTextarea[j].disabled = false;
                        }
                        return true;
                    }

                    var edition = true;
                    function enableEdit(but) {
                        var nodeList = document.getElementsByTagName("input");
                        var nodeListTextarea = document.getElementsByTagName("textarea");
                        for (var j = 0; j < nodeList.length - 3; j++) {
                            if (edition) {
                                nodeList[j].disabled = false;
                            } else {
                                nodeList[j].disabled = true;
                            }
                        }
                        for (var j = 0; j < nodeListTextarea.length - 1; j++) {
                            if (edition) {
                                nodeListTextarea[j].disabled = false;
                            } else {
                                nodeListTextarea[j].disabled = true;
                            }
                        }

                        edition === true ? but.innerHTML = "Bloquar" : but.innerHTML = "Editar";

                        edition = !edition;
                    }

                    var reject = document.getElementById('reject');
                    var accept = document.getElementById('accept');


                    if (reject && accept) {
                        reject.addEventListener('click', function () {
                            clear();
                            
                            $("#reserveForm").append('<input type="hidden" name="action" value="reject" /> ');
                            document.getElementById('reserveForm').submit();
                        }, false);

                        accept.addEventListener('click', function () {
                            clear();
                            
                            $("#reserveForm").append('<input type="hidden" name="action" value="accept" /> ');
                            document.getElementById('reserveForm').submit();
                        }, false);
                    }



                    $('#popup').delay(5000).fadeOut('slow');
                </script>