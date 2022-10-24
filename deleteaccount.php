<?php

include('db_connector.inc.php');
session_start();

// variablen initialisieren
$error = $message = '';

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.";
    }
    if (!empty($error)) {
        $message = $error;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $username = $_SESSION['username'];

        $query = "select userid from user where username = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result=$stmt->get_result();
		$userid = $result->fetch_assoc();
        
        $query = "delete from croak where user_userid = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userid['userid']);
        $stmt->execute();

        $query = "delete from user where userid = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $userid['userid']);
        $stmt->execute();

        header('Location:dataChangedSuccessfully.php');
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administrationsbereich</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/aa92474866.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    require __DIR__ . '/uielements.php';
    echo showNavigationBar($_SESSION);
    ?>
    <div class="container">
    <br>
        <h1>Administrationbereich / Account löschen</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else {
            echo "<div class=\"alert alert-danger\" role=\"alert\"> Mit dieser Aktion wirst du deinen Account und alle verknüpften Daten unwiederruflich löschen </div>";
            echo "<br>Bist du dir ganz sicher?<br><br>";
            echo '<form action="" method="post">';
                echo '<button type="submit" name="buttonDelete" id="delete-btn" class="btn btn-danger">Account löschen</button>';
            echo '</form>';
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>