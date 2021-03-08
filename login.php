<?php
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['email'])) {
    header('location: notes.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <?php

    // declare global variables
    // set to defaults
    $login = $password = "";
    $loginError = $passwordError = $enterError = "";

    // detects mistakes
    $success = true;

    // if there is POST request, check form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // else check name and match with regex
        $login = checkInput($_POST['login']);

        if (!preg_match("/^[a-zA-Z0-9]/", $login)) {
            $loginError = "Only letters and digits allowed";
            $success = false;
        }

        // math the password
        $password = checkInput($_POST['password']);

        if (!preg_match("/^[a-zA-Z0-9]/", $password)) {
            $passwordError = "Only letters and digits allowed";
            $success = false;
        }

        if ($success) {
            /* database connection */

            // db config
            $dbserver = "localhost";
            $dbusername = "root";
            $dbpassword = "";
            $dbname = "notes";
            $dbport = 3306;
            $dbTableName = "users";

            try {
                // try to connect 
                $pdo = new PDO("mysql:host=$dbserver;port=$dbport;dbname=$dbname", $dbusername, $dbpassword);

                // set the error mode
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $statement = $pdo->prepare("SELECT password FROM $dbTableName WHERE (login='$login' OR email='$login')");
                $statement->execute();

                $result = $statement->setFetchMode(PDO::FETCH_ASSOC);

                $resultingArray = $statement->fetchAll();

                if (count($resultingArray) > 0) {
                    if (password_verify($password, $resultingArray[0]['password'])) {
                        // get username
                        $statement = $pdo->prepare("SELECT login, email FROM $dbTableName WHERE (login='$login' OR email='$login') LIMIT 1");
                        $statement->execute();

                        $result = $statement->setFetchMode(PDO::FETCH_ASSOC);

                        $selected = $statement->fetchAll()[0];

                        $_SESSION['username'] = $selected['login'];
                        $_SESSION['email'] = $selected['email'];
                        header('location: notes.php');
                    } else {
                        $enterError = "Wrong username or password";
                    }
                } else {
                    $enterError = "Wrong username or password";
                }

                // close the connection
                $pdo = null;
            } catch (PDOException $e) {
                // if there is any error, print it
                echo "Connection failed: " . $e->getMessage();
            }
        }
    }

    // checks user input
    function checkInput($element)
    {
        $element = trim($element);
        // remove '\'
        $element = stripslashes($element);
        $element = htmlspecialchars($element);

        return $element;
    }
    ?>

    <div class="wrapper">
        <div class="login-form">
            <h1>Notes</h1>
            <span id="enter-error" class="error"><?php echo $enterError ?></span>
            <form id="form" action="" method="POST">
                <label for="login"></label>
                <input type="text" name="login" id="login" placeholder="username or email" maxlength="254" required value="<?php echo $login ?>" autocomplete="off">
                <span id="login-error" class="error"><?php echo $loginError ?></span>
                <br> <br>
                <label for="password"></label>
                <input type="password" id="password" name="password" placeholder="password" maxlength="24" required value="<?php echo $password ?>" autocomplete="off">
                <span id="password-error" class="error"><?php echo $passwordError ?></span>
                <br> <br>
                <input id="submit-btn" type="submit" value="Login"> <br> <br>
            </form>
            <p class="sign-up">Don't have an account yet? <a id="link" href="./sign_up.php">Sign Up</a></p>
        </div>
    </div>
</body>

</html>