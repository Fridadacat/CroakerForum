<?php

session_start();
include('db_connector.inc.php');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// variablen initialisieren
$error = $message = '';

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.";
        //header('Location:login.php'); //redirect
        //die();
    } else {
        $username = $_SESSION['username'];
        $message .= " Servus $username!";
    }

    if (!empty($error)) {
        $message = $error;
    }

    function getLastTwentyCroaks($mysqli) {

        $result = mysqli_query($mysqli, "select * from croak order by croakid desc limit 20");
        $returnString = "";

        while($row = mysqli_fetch_row($result)) {
            $isUserCreator = false;
            $croakId = $row[0];
            $userId = $row[1]; //1 = user_userId
            $dateTime = $row[2]; //2 = dateTime
            $croak = $row[3]; //3 = text


            $username = getUsername($mysqli, $userId);

            if ($username === $_SESSION['username']) {
                $isUserCreator = true;
            }

            $returnString .= showCroak($username, $croak, $dateTime, $isUserCreator, $croakId, true);
        
        }

        return $returnString;
    }

    function getUsername($mysqli, $userId) {
        $query = "select username from user where userid = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$result=$stmt->get_result();
		$username = $result->fetch_assoc();
        return $username['username'];
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Croakerfeed</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/aa92474866.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php
    require __DIR__ . '/uielements.php';
    echo showNavigationBar($_SESSION);
    ?>
    <div class="container">
    <br>
        <h1>Croakerfeed</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)) {
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        ?>
    </div>

    <?php
    echo getLastTwentyCroaks($mysqli);
    echo showPostButton($_SESSION);
    ?>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>