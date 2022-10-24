<?php

session_start();
include('db_connector.inc.php');

// variablen initialisieren
$error = $message = '';
$isAllowedToEdit = false;

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.<br>";
        //header('Location:login.php'); //redirect
        //die();
    }

    $croakId = $_GET['croakId'];
    $query = "select user_userid from croak where croakid = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $croakId);
    $stmt->execute();
    $result=$stmt->get_result();
    $userid1 = $result->fetch_assoc();

    $query = "select userid from user where username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result=$stmt->get_result();
    $userid2 = $result->fetch_assoc();

    if ($userid1['user_userid'] == $userid2['userid']) {
        $isAllowedToEdit = true;
    }


    if (!empty($error)) {
        $message = $error;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if(array_key_exists('buttonUpdate', $_POST)) {

            $dateTime = date("Y-m-d H:i:s");
            $croak = $_POST['croak'];

            if ($croak == null || empty(trim($croak))) {
                $error .= "Sie können keinen leeren Croak posten<br>";
            }
    
            if ($croak === findCroakById($mysqli)) {
                $error .= "Sie müssen ihren croak bearbeiten um ihn erneut posten zu können";
            }
            
            if (empty($error)) {
                update($mysqli, $croakId, $dateTime, $croak);
            }
        }

        if(array_key_exists('buttonDelete', $_POST)) {
            delete($mysqli, $croakId);
        }

        if(array_key_exists('buttonReport', $_POST)) {
            $error.= "Wir haben ihre Meldung erhalten, allerdings legen wir hohen Wert auf Meinungsfreiheit. Aus diesem Grund wird niemand ihren Report begutachten...<br>Wenn sie trotzdem gegen den Verfasser des Croaks vorgehen wollen, raten wir ihnen dies wie zivilisierte Menschen mit Gewalt zu regeln!";
        }

    }

    function update($mysqli, $croakId, $dateTime, $croak) {
        if (empty($error)) {
            $query = "update croak set created=?, text=? where croakid = ".$croakId;
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $dateTime, $croak);
            $stmt->execute();
            header('Location:myspace.php');
        }
    }

    function delete($mysqli, $croakId) {
        if (empty($error)) {
            $query = "delete from croak where croakid = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("s", $croakId);
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

    function findCroakById($mysqli) {
        $query = "select text from croak where croakid = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $_GET['croakId']);
		$stmt->execute();
		$result=$stmt->get_result();
		$text = $result->fetch_assoc();
        return $text['text'];
    }

    function findCroakDateById($mysqli) {
        $query = "select created from croak where croakid = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $_GET['croakId']);
		$stmt->execute();
		$result=$stmt->get_result();
		$text = $result->fetch_assoc();
        return $text['created'];
    }

    function findCroakCreatorById($mysqli, $userid) {
        $query = "select username from user where userid = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("i", $userid);
		$stmt->execute();
		$result=$stmt->get_result();
		$text = $result->fetch_assoc();
        return $text['username'];
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
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php
    require __DIR__ . '/uielements.php';
    echo showNavigationBar($_SESSION);
    ?>
    <div class="container">
    <br>
        <h1>Croak</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)) {
            echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
        ?>
        <br>
        <?php
            echo "Der Croak mit der ID ".$croakId.":";
        ?>
    </div>
    <br>
    <?php
        if ($isAllowedToEdit == true) {
            echo '<form action="" method="post">';
                echo '<div class="input-group">';
                echo '<textarea name="croak" id="inputField" required value="<?php echo htmlspecialchars($croak) ?>" aria-label="Large" aria-describedby="inputGroup-sizing-sm" placeholder="Dein Croak...">'.filter_var(findCroakById($mysqli), FILTER_SANITIZE_SPECIAL_CHARS).'</textarea>';
                echo '</div>';
                echo '<br><button type="submit" name="buttonUpdate" id="submitButton" value="submit" class="btn btn-primary">Update!</button>';
            echo '</form>';
            echo '<form action="" method="post">';
                echo '<button type="submit" name="buttonDelete" id="delete-btn" class="btn btn-danger">Löschen</button>';
            echo '</form>';
        } else {
            echo showCroak(findCroakCreatorById($mysqli, $userid1['user_userid']), findCroakById($mysqli), findCroakDateById($mysqli), false, $croakId, false);
            echo '<form action="" method="post">';
                echo '<br><button type="submit" name="buttonReport" id="submitButton" class="btn btn-warning">Beitrag melden!</button>';
            echo '</form>';
            }
    ?>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>