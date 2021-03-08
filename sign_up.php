<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/sign_up.css">
</head>

<body>
    <?php

    // declare global variables
    // set to defaults
    $login = $email = $password = $repeatPassword = "";
    $loginError = $emailError = $passwordError = $repeatError = "";

    $success = true;

    // if there is POST request, check form
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // else check name and match with regex
        $login = checkInput($_POST['login']);

        if (!preg_match("/^[a-zA-Z0-9]{3,}/", $login)) {
            if (strlen($login) < 3)
                $loginError = "Login must be at least 3 letters";
            else
                $loginError = "Only letters and digits allowed";
            $success = false;
        }

        $email = checkInput($_POST['email']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailError = "Invalid email format";
            $success = false;
        }

        $password = checkInput($_POST['password']);

        if (!preg_match("/^[a-zA-Z0-9]{6,}/", $password)) {
            if (strlen($password) < 6)
                $passwordError = "Password must be at least 6 letters";
            else
                $passwordError = "Only letters and digits allowed";
            $success = false;
        }

        $repeatPassword = checkInput($_POST['password-repeat']);

        if ($repeatPassword !== $password)
            $success = false;

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

                $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
                $pdo->exec("CREATE TABLE IF NOT EXISTS $dbTableName(
        personId INT NOT NULL AUTO_INCREMENT,
        login VARCHAR(40) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(80) NOT NULL,
        PRIMARY KEY(personId)
    )");

                $emailExist = false;
                $loginExist = false;

                $statement = $pdo->prepare("SELECT personId FROM $dbTableName WHERE login='$login'");
                $statement->execute();

                $result = $statement->setFetchMode(PDO::FETCH_ASSOC);

                if (count($statement->fetchAll()) > 0) {
                    $loginExist = true;
                    $loginError = "This login is already taken";
                }

                $statement = $pdo->prepare("SELECT personId FROM $dbTableName WHERE email='$email'");
                $statement->execute();

                if (count($statement->fetchAll()) > 0) {
                    $emailExist = true;
                    $emailError = "This email is already taken";
                }

                if (!($emailExist || $loginExist)) {
                    $statement = $pdo->prepare("INSERT INTO $dbTableName(login, email, password)
            VALUES (:login, :email, :password)");

                    $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $login = strtolower($login);
                    $email = strtolower($email);

                    $statement->bindParam(':login', $login);
                    $statement->bindParam(':email', $email);
                    $statement->bindParam(':password', $encryptedPassword);

                    $statement->execute();

                    // close the connection
                    $pdo = null;

                    header('location: ./login.php');
                }
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
            <h1>Register</h1>
            <form id="form" action="<?php htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <label for="login"></label>
                <input type="text" name="login" id="login" placeholder="login(username)" maxlength="254" required value="<?php echo $login ?>" autocomplete="off">
                <span class="error" id="login-error"><?php echo $loginError ?></span>
                <br> <br>
                <label for="email"></label>
                <input type="text" name="email" id="email" placeholder="email" maxlength="254" required value="<?php echo $email ?>" autocomplete="off">
                <span class="error" id="email-error"><?php echo $emailError ?></span>
                <br> <br>
                <label for="password"></label>
                <input type="password" id="password" name="password" placeholder="password" maxlength="24" required value="<?php echo $password ?>" autocomplete="off">
                <span class="error" id="password-error"><?php echo $passwordError ?></span>
                <br> <br>
                <label for="password-repeat"></label>
                <input type="password" id="password-repeat" name="password-repeat" placeholder="repeat password" maxlength="24" required value="<?php echo $repeatPassword ?>" autocomplete="off">
                <span class="error" id="repeated-error"></span>
                <br> <br>
                <input id="submit-btn" type="submit" value="Login"> <br> <br>
            </form>
            <p class="sign-up">Already have an account? <a id="link" href="./login.php">Login</a></p>
        </div>
    </div>

    <script src="./js/sign_up.js"></script>
</body>

</html>