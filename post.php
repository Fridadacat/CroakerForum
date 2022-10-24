<?php

session_start();
include('db_connector.inc.php');

// variablen initialisieren
$error = $message = '';

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.";
        //header('Location:login.php'); //redirect
        //die();
    } else {
        $message .= "Willkommen auf der Post Seite! Hier kannst du deinen Post verfassen!";
    }

    if (!empty($error)) {
        $message = $error;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
        $croak = $_POST['croak'];
        $username = $_SESSION['username'];
        $userId = getUserId($username, $mysqli);
        $dateTime = date("Y-m-d H:i:s");

        if ($croak == null || empty(trim($croak))) {
            $error .= "Sie können keinen leeren Croak posten";
        }

    
        // keine Fehler vorhanden
        if (empty($error)) {
            $query = "insert into croak (user_userid, created, text) values (?,?,?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("iss", $userId['userid'], $dateTime, $croak);
            $stmt->execute();
            header('Location:myspace.php');
        }
    }

    function getUserId($username, $mysqli) {
        $query = "select userid from user where username = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result=$stmt->get_result();
		$userid = $result->fetch_assoc();
        return $userid;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Croak Verfassen</title>

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
        <br>
        Teile der Welt mit was dich so beschäftigt:
    </div>
    <br>
    <form action="" method="post">
        <div class="input-group">
            <textarea name="croak" id="inputField" required value="<?php echo htmlspecialchars($croak) ?>" aria-label="Large" aria-describedby="inputGroup-sizing-sm" placeholder="Dein Croak..."></textarea>
        </div>
        <br><button type="submit" name="button" id="submitButton" value="submit" class="btn btn-primary">Croak!</button>
    </form>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>