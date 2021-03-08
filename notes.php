<?php
session_start();

// if user is not logged in, 
if (!isset($_SESSION['username']) || !isset($_SESSION['email'])) {
    header('location: login.php');
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];

$dbserver = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "notes";
$dbport = 3306;
$dbTableName = $username;

try {
    // try to connect 
    $pdo = new PDO("mysql:host=$dbserver;port=$dbport;dbname=$dbname", $dbusername, $dbpassword);

    // set the error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS $dbTableName(
    noteID INT NOT NULL AUTO_INCREMENT,
    noteName VARCHAR(80) NOT NULL,
    noteText LONGTEXT NOT NULL,
    PRIMARY KEY(noteID)
)");
} catch (PDOException $e) {
    // if there is any error, print it
    echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <link rel="stylesheet" href="./css/notes.css">
</head>

<body>
    <header>
        <span id="username"><?php echo $username ?></span>

        <a href="logout.php" id="logout">Logout</a>
    </header>

    <div class="wrapper">
        <div class="sidebar">
            <div class="info">
                <p class="note-count">Notes: 30</p>
                <input type="button" value="Add note" class="add-note">
            </div>
            <div class="notes">
                <?php

                ?>
                <div class="note">
                    <p class="text">Note. Just note.</p>
                </div>
            </div>
        </div>

        <div class="note-content">
            <div class="note-name">
                <input type="text" maxlength="80" placeholder="Note name" id="name">
                <button type="submit" id="save-button">Save</button>
            </div>
            <div class="note-text">
                <textarea name="note" placeholder="Note text"></textarea>
            </div>
        </div>
    </div>
    <script>
        const textArea = document.querySelector('.note-text');
        const notes = document.querySelectorAll('.note');
        const noteName = document.querySelector('#name');
        const saveButton = document.querySelector('#save-button');

        notes.forEach((note) => {
            note.addEventListener('click', (e) => {
                <?php
                // TODO: read the note text
                $statement = $pdo->prepare("SELECT noteText WHERE noteName='");
                ?>
                textArea.textContent = note.children[0].textContent;
            });
            note.addEventListener('mouseover', (e) => {
                note.style = "background-color: #bbb;";
            });
            note.addEventListener('mouseout', (e) => {
                note.style = "background-color: #eee";
            });
        });

        saveButton.addEventListener('click', (e) => {
            <?php

            $noteName = "<script>document.writeln(noteName);</script>";
            $noteText = "<script>document.writeln(textArea.textContent);</script>";

            $statement = $pdo->prepare("INSERT INTO $dbTableName(noteName, noteText)
                VALUES (:noteName, :noteText)");

            $statement->bindParam(":noteName", $noteName);
            $statement->bindParam(":noteText", $noteText);

            $statement->execute();
            ?>
        });
    </script>
</body>

</html>