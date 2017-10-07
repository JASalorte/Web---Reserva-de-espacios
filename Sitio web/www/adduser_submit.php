<?php
/* * * begin the session ** */
session_start();

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
        $username = $stmt->fetchColumn();

        /*         * * if we have no something is wrong ** */
        $valid = false;
        if ($username == false) {
            $message = 'Access Error';
        } else {
            $message = 'Welcome ' . $username;
            $valid = true;
        }
    } catch (Exception $e) {
        /*         * * if we are here, something is wrong in the database ** */
        $message = 'We are unable to process your request. Please try again later"';
    }
}

/*** first check that both the username, password and form token have been sent ***/
if(!isset( $_POST['username'], $_POST['password'], $_POST['form_token']))
{
    $message = 'Please enter a valid username and password';
}
/*** check the form token is valid ***/
elseif( $_POST['form_token'] != $_SESSION['form_token'])
{
    $message = 'Invalid form submission';
}
/*** check the username is the correct length ***/
elseif (strlen( $_POST['username']) > 20 || strlen($_POST['username']) < 4)
{
    $message = 'Incorrect Length for Username';
}
/*** check the password is the correct length ***/
elseif (strlen( $_POST['password']) > 20 || strlen($_POST['password']) < 4)
{
    $message = 'Incorrect Length for Password';
}
/*** check the username has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['username']) != true)
{
    /*** if there is no match ***/
    $message = "Username must be alpha numeric";
}
/*** check the password has only alpha numeric characters ***/
elseif (ctype_alnum($_POST['password']) != true)
{
        /*** if there is no match ***/
        $message = "Password must be alpha numeric";
}
else
{
    /*** if we are here the data is valid and we can insert it into database ***/
    $user_username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $user_password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    /*** now we can encrypt the password ***/
    $user_password = sha1( $user_password );

    require './mysqlData.php';
    
    try
    {
        $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        /*** $message = a message saying we have connected ***/

        /*** set the error mode to excptions ***/
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*** prepare the insert ***/
        $stmt = $dbh->prepare("INSERT INTO users (username, password ) VALUES (:username, :password )");

        /*** bind the parameters ***/
        $stmt->bindParam(':username', $user_username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $user_password, PDO::PARAM_STR, 40);

        /*** execute the prepared statement ***/
        $stmt->execute();

        /*** unset the form token session variable ***/
        unset( $_SESSION['form_token'] );

        /*** if all is done, say thanks ***/
        $message = 'New user added';
    }
    catch(Exception $e)
    {
        /*** check if the username already exists ***/
        if( $e->getCode() == 23000)
        {
            $message = 'Username already exists';
            //$message = "INSERT INTO users (username, password ) VALUES ('$user_username', '$user_password' )";
        }
        else
        {
            /*** if we are here, something has gone wrong with the database ***/
            $message = 'We are unable to process your request. Please try again later"';
        }
    }
}
?>

<html>
<head>
<title>PHPRO Login</title>
</head>
<body>
<p><?php echo $message; ?>
    <br/>
<a href="/">Get back</a>
</body>
</html>