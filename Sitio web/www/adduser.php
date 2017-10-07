<?php
/* * * begin the session ** */
session_start();

/*Instrucciones para poner la restricción de login*/

/*Comentar desde aquí*/
$valid = true;
/*Hasta aquí*/

/*Descomentar desde aquí*/
/*if (!isset($_SESSION['user_id'])) {
    $message = 'You must be logged in to access this page';

    $valid = false;
} else {
    try {
        require './mysqlData.php';

        $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $dbh->prepare("SELECT username FROM users WHERE id = :id");

        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        $username = $stmt->fetchColumn();

        $valid = false;
        if ($username == false) {
            $message = 'Access Error';
        } else {
            $message = 'Welcome ' . $username;
            $valid = true;
        }
    } catch (Exception $e) {
        $message = 'We are unable to process your request. Please try again later"';
    }
}*/

/*Hasta aquí*/
/*Fin de la instrucciones*/

/* * * set a form token ** */
$form_token = md5(uniqid('auth', true));

/* * * set the session form token ** */
$_SESSION['form_token'] = $form_token;
?>



<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="jquery-1.11.3.min.js"></script>
        <link rel="stylesheet" type="text/css" href="CSS/cerulean.css">


        <title>Gestión de espacio - Creación de usuario</title>
    </head>

    <body>
        <?php if ($valid === TRUE): ?>
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
                </div>
            </div>
        </nav>

            <div class="container-fluid">
                <div class="row">

                    <div id="blank" class="col-lg-4 col-md-4 col-sm-4 col-xs-0">

                    </div>

                    <div id="selector" class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <b class="panel-title">Creación de usuario</b>
                                    </div>
                                    <div class="panel-body">
                                        <form action="adduser_submit.php" method="post">
                                            <fieldset>
                                                <p>
                                                    <label for="username">Username</label>
                                                    <input type="text" id="username" name="username" value="" maxlength="20" />
                                                </p>
                                                <p>
                                                    <label for="password">Password</label>
                                                    <input type="password" id="password" name="password" value="" maxlength="20" />
                                                </p>
                                                <p>
                                                    <input type="hidden" name="form_token" value="<?php echo $form_token; ?>" />
                                                    <input type="submit" value="Crear" />
                                                </p>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php header("HTTP/1.0 404 Not Found"); ?>
        <?php endif; ?>
    </body>
</html>
