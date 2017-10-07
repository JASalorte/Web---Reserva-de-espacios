<?php
/* * * begin our session ** */
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = false;
    /*     * * check if the users is already logged in ** */
    if (isset($_SESSION['user_id'])) {
        $message = 'Users is already logged in.';
        
    }
    /*     * * check that both the username, password have been submitted ** */
    if (!isset($_POST['user_username'], $_POST['user_password'])) {
        $message = 'Please enter a valid username and password.';
    }
    /*     * * check the username is the correct length ** */ elseif (strlen($_POST['user_username']) > 20 || strlen($_POST['user_username']) < 4) {
        $message = 'Incorrect Length for Username.';
    }
    /*     * * check the password is the correct length ** */ elseif (strlen($_POST['user_password']) > 20 || strlen($_POST['user_password']) < 4) {
        $message = 'Incorrect Length for Password.';
    }
    /*     * * check the username has only alpha numeric characters ** */ elseif (ctype_alnum($_POST['user_username']) != true) {
        /*         * * if there is no match ** */
        $message = "Username must be alpha numeric.";
    }
    /*     * * check the password has only alpha numeric characters ** */ elseif (ctype_alnum($_POST['user_password']) != true) {
        /*         * * if there is no match ** */
        $message = "Password must be alpha numeric.";
    } else {
        /*         * * if we are here the data is valid and we can insert it into database ** */
        $user_username = filter_var($_POST['user_username'], FILTER_SANITIZE_STRING);
        $user_password = filter_var($_POST['user_password'], FILTER_SANITIZE_STRING);

        /*         * * now we can encrypt the password ** */
        $user_password = sha1($user_password);
        
        

        /*         * * connect to database ** */
        /*         * * mysql hostname ** */
       

        try {
            
            require './mysqlData.php';
            $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            /*             * * $message = a message saying we have connected ** */

            /*             * * set the error mode to excptions ** */
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /*             * * prepare the select statement ** */
            $stmt = $dbh->prepare("SELECT id, username, password FROM users 
                    WHERE username = :username AND password = :password");

            /*             * * bind the parameters ** */
            $stmt->bindParam(':username', $user_username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $user_password, PDO::PARAM_STR, 40);
            
            

            /*             * * execute the prepared statement ** */
            $stmt->execute();

            /*             * * check for a result ** */
            $user_id = $stmt->fetchColumn();

            /*             * * if we have no result then fail boat ** */

            if ($user_id == false) {
                $message = 'Login Failed.';
            }
            /*             * * if we do have a result, all is well ** */ else {
                /*                 * * set the session user_id variable ** */
                $_SESSION['user_id'] = $user_id;
//$_SESSION['user_name'] = $user_name;

                /*                 * * tell the user we are logged in ** */
                $valid = true;
                header('Location: /');
                $message = 'You are now logged in.';
            }
        } catch (Exception $e) {
            /*             * * if we are here, something has gone wrong with the database ** */
            $message = 'We are unable to process your request. Please try again later.';
        }
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <script src="jquery-1.11.3.min.js"></script>
        
        <link rel="stylesheet" type="text/css" href="CSS/cerulean.css">


        <title>Gestión de espacio - Login</title>


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

        <div class="container-fluid">
            <div class="row">

                <div id="blank" class="col-lg-4 col-md-4 col-sm-4 col-xs-0">

                </div>

                <div id="selector" class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if (($_SERVER["REQUEST_METHOD"] == "POST") && ($valid === false)): ?>
                                <div class="alert alert-dismissible alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    <strong>Oh snap!</strong> <?php echo $message; ?> <a href="#" class="alert-link">Change it</a> and try submitting again.
                                    
                                </div>
                            <?php endif; ?>




                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <b class="panel-title">Login</b>
                                </div>
                                <div class="panel-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <fieldset>
                                            <p>
                                                <label for="user_username">Username</label>
                                                <input type="text" id="user_username" name="user_username" value="" maxlength="20" />
                                            </p>
                                            <p>
                                                <label for="user_password">Password</label>
                                                <input type="user_password" id="user_password" name="user_password" value="" maxlength="20" />
                                            </p>
                                            <p>
                                                <input type="submit" value="Login" />
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



    </body>
</html>

<script>
    function logout() {
        $.get("logout.php");
        parent.window.location.reload();
        return false;
    }

</script>