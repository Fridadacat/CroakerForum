<?php

session_start();
include('db_connector.inc.php');

// variablen initialisieren
$error = $message = '';

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.";
        header('Location:login.php'); //redirect
        die();
    } else {
        $username = $_SESSION['username'];
        $message .= " Servus $username!";
    }

    if (!empty($error)) {
        $message = $error;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrationbereich</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/aa92474866.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Session Handling</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">

                <?php
                    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
                        // TODO - wenn Session nicht personalisiert - Link zu Login / Register anzeigen
                        echo '<li class="nav-item"><a class="nav-link" href="register.php">Registrierung</a></li>';
                        echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                        die();
                    } else {
                        // TODO - wenn Session personalisiert ist - Link zu Logout anzeigen
                        echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Administrationbereich</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)) {
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>