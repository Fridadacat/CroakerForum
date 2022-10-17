<?php
include('db_connector.inc.php');
session_start();

// variablen initialisieren
$error = $message = $oldpassword = $newpassword = $newpasswordrepeat = '';

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.";
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Ausgabe des gesamten $_POST Arrays zum debuggen
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    
        $oldpassword = trim($_POST['oldpassword']);
        $newpassword = trim($_POST['newpassword']);
        $newpasswordrepeat = trim($_POST['newpasswordrepeat']);

        $sendData = true;

        validateOldPassword($oldpassword, $mysqli) != true ? $sendData = false && $error .= "Ihr altes Passwort ist inkorrekt.<br>" : null;
        checkIfNewPasswordsAreEqual($newpassword, $newpasswordrepeat) != true ? $sendData = false && $error .= "Die neuen Passwörter stimmen nicht miteinander überein.<br>" : null;
        checkIfNewPasswordDoesNotEqualOldPassword($newpassword, $mysqli) != true && $error .= "Ihr neues Passwort etsprich ihrem alten Passwort.<br>" ? $sendData = false : null;

        if ($sendData = true) {
            //validate password
            if (isset($newpassword)) {
                if (empty($newpassword)) {
                    $error . "Bitte gib dein Passwort an!<br>";
                } else if (strlen($newpassword) < 8) {
                    $error .= "Ihr Passwort darf nicht kürzer als 8 Zeichen Sein!<br>";
                } else if (strlen($newpassword) > 255) {
                    $error .= "Ihr Passwort darf nicht länger als 255 Zeichen Sein!<br>";
                } else if (!preg_match('/[A-Z]/', $newpassword)) {
                    $error .= "Ihr Passwort benötigt mindestens einen Grossbuchstaben!<br>";
                } else if (!preg_match('/[a-z]/', $newpassword)) {
                    $error .= "Ihr Passwort benötigt mindestens einen Kleinbuchstaben!<br>";
                } else if (!preg_match('/[0-9]/', $newpassword)) {
                    $error .= "Ihr Passwort benötigt mindestens eine Zahl!<br>";
                } else if (strpos($newpassword, '+' || '-' || '*' || '%' || '&' || '/' || '(' || ')' || '=' || '?' || '!' || '$')) {
                    $error .= "Ihr Passwort benötigt mindestens einen Sonderzeichen. Zugelassen sind: +,-,*,%,&,/,(,),=,?,!,$.<br>";
                }
            }
        }
    }

    if (!empty($error)) {
        $message = $error;
    } else {

        $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
		if ($hashedPassword === false) {
			$error .= "Ihr passwort konnte nicht gehasht werden und ist somit nicht sicher! <br>";
			$hashedPassword = $password;
		}

        $username = $_SESSION['username'];
        // SQL-Statement erstellen
        $query = "update users set password = ? where username = $username";
        // SQL-Statement vorbereiten
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $error .= 'prepare() failed ' . $mysqli->error . '<br />';
        }
        // Daten an das SQL-Statement binden
        if (!$stmt->bind_param('s', $hashedPassword)) {
            $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
        }
        // SQL-Statement ausführen
        if (!$stmt->execute()) {
            $error .= 'execute() failed ' . $mysqli->error . '<br />';
        }

        $stmt->close();
        header('Location:dataChangedSuccessfully.php');
    }

    function validateOldPassword($password, $mysqli) {
        $query = "select password from users where username = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $_SESSION['username']);
		$stmt->execute();
		$result=$stmt->get_result();
		$dbPassword = $result->fetch_assoc();

        if(password_verify($password, $dbPassword["password"])) {
            return true;
        } else {
            return false;
        }
    }

    function checkIfNewPasswordsAreEqual($password1, $password2) {
        if($password1 === $password2) {
            return true;
        } else {
            return false;
        }
    }

    function checkIfNewPasswordDoesNotEqualOldPassword($newpassword, $mysqli) {
        $query = "select password from users where username = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $_SESSION['username']);
		$stmt->execute();
		$result=$stmt->get_result();
		$dbPassword = $result->fetch_assoc();

        if(password_verify($newpassword, $dbPassword["password"])) {
            return false;
        } else {
            return true;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passwort ändern</title>

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
        <h1>Administrationbereich / Passwort ändern</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        }?>
        <form action="" method="post">
			<div class="form-group">
				<label for="password">Altes Passwort:</label>
				<input type="password" name="oldpassword" class="form-control" id="password" maxlength="255" required value="<?php echo htmlspecialchars($password) ?>" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute">
			</div>
            <div class="form-group">
				<label for="password">Neues Passwort:</label>
				<input type="password" name="newpassword" class="form-control" id="password" maxlength="255" required value="<?php echo htmlspecialchars($password) ?>" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute">
			</div>
            <div class="form-group">
				<label for="password">Neues Passwort wiederholen:</label>
				<input type="password" name="newpasswordrepeat" class="form-control" id="password" maxlength="255" required value="<?php echo htmlspecialchars($password) ?>" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute">
			</div>
			<button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
			<button type="reset" name="button" value="reset" class="btn btn-warning">Löschen</button>
		</form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>