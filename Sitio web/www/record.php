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
        require 'mysqlData.php';


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
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="jquery-1.11.3.min.js"></script>
        <script src="jquery-ui.min.js"></script>
        <script src="jquery.ui.datepicker-es.js"></script>
        
        <link rel="stylesheet" type="text/css" href="CSS/jquery.ui.datepicker-style.css">
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
                            <li class="active"><a href="record.php">Historial</a></li>
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
                                <div class="col-lg-10 col-md-10">
                                    <?php
                                            require './phpdata.php';
                                            $id = filter_input(INPUT_GET, 'id');
                                            if ($id === null) :
                                    ?>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="panel-body">
                                                <div id="divControls">
                                                    <input type="button" value="Hoy" id="btToday" class="left-align">
                                                    <input type="button" value="<<" id="btPrev2" class="left-align">
                                                    <input type="button" value="<" id="btPrev" class="left-align">
                                                    <div id="divDate" class="left-align">
                                                        <input id="inCalendar" class="left-align" type="text" readonly="readonly">
                                                        <div class="left-align" id="divDay">Miércoles</div>
                                                    </div>
                                                    <input type="button" value=">" id="btNext" class="left-align">
                                                    <input type="button" value=">>" id="btNext2" class="left-align">
                                                </div>
                                            </div>
                                        </div>
                                           <?php endif; ?>
                                    </div>
                                    <div class="row">
                                        <div id="record" class="col-lg-12 col-md-12">
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
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

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
                                                        <td align="right">
                                                            <a class="btn btn-default" href="record.php">Volver</a>
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

                    var weekday = new Array(7);
                    weekday[0] = "Lunes";
                    weekday[1] = "Martes";
                    weekday[2] = "Miércoles";
                    weekday[3] = "Jueves";
                    weekday[4] = "Viernes";
                    weekday[5] = "Sábado";
                    weekday[6] = "Domingo";
                    var actualDate;
                    var minDate;
                    var maxDate;
                    $('#btPrev2').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() - 7);
                        if (temp.valueOf() > minDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    $('#btPrev').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() - 1);
                        if (temp.valueOf() > minDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    $('#btToday').click(function () {
                        $("#inCalendar").datepicker('setDate', new Date());
                        $('#divDay').html(weekday[($("#inCalendar").datepicker('getDate').getUTCDay()) ]);
                        
                        changeRecord();
                    });
                    $('#btNext').click(function () {

                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() + 1);
                        
                        if (temp.valueOf() < maxDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    $('#btNext2').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() + 7);
                        if (temp.valueOf() < maxDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    function loadFields(date) {
                        $("#inCalendar").datepicker('setDate', date);
                        $('#divDay').html(weekday[date.getUTCDay()]);
                        
                        changeRecord();
                    }

                    $(function () {
                        $("#inCalendar").datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd-mm-yy",
                            minDate: "01-01-2015",
                            maxDate: 0,
                            onSelect: function (dateText, inst) {
                                $('#divDay').html(weekday[($(this).datepicker('getDate').getUTCDay())]);
                                changeRecord(); 
                            }
                        });
                        $("#inCalendar").datepicker('setDate', new Date());
                        minDate = new Date("December 31, 2014");
                        maxDate = new Date();                    
                        maxDate.setDate(maxDate.getDate());
                        $('#divDay').html(weekday[($("#inCalendar").datepicker('getDate').getUTCDay()) ]);
                        changeRecord();
                        
                    });


                    function changeRecord() {
                        var d = $("#inCalendar").datepicker('getDate');
                        var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " 00:00:00";
                        var date2 = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " 23:59:59";

                        $.ajax({
                            url: 'recordPHP.php',
                            type: 'POST',
                            data: {var1: date, var2: date2},
                            success: function (data) {
                                
                                document.getElementById("record").innerHTML=data;
                            }
                        });
                    }



                    $('#popup').delay(5000).fadeOut('slow');

                </script>