<?php
include('db_connector.inc.php');
session_start();

// variablen initialisieren
$error = $message = $username = $password = '';
$oldusername = $_SESSION['username'];

// TODO -  Wenn personalisierte Session: Begrüssen des Benutzers mit Benutzernamen aus der Session
    if (!isset($_SESSION['loggedin']) or !$_SESSION['loggedin']) {
        $error .= "Sie sind nicht angemeldet, melden Sie sich bitte auf der  <a href='login.php'>Login-Seite</a> an.";
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Ausgabe des gesamten $_POST Arrays zum debuggen
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
    
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $sendData = true;

        $isPasswordCorrect = validatePassword($password, $mysqli);
        $areTheUsernamesEqual = validateUsername($username);
        $doesUsernameAlreadyExist = checkIfUsernameAlreadyExists($username, $mysqli);
        //check if name allready exists

        if ($isPasswordCorrect != true) {
            $sendData = false;
            $error .= "Ihr altes Passwort ist inkorrekt.<br>";
        }

        if ($areTheUsernamesEqual != true) {
            $sendData = false;
            $error .= "Ihr neuer Benutzername entspricht dem jetzigen.<br>";
        }

        if ($doesUsernameAlreadyExist != true) {
            $sendData = false;
            $error .= "Der Benutzername ist bereits vergeben.<br>";
        }

        if ($sendData = true) {
            //validate password
            if (isset($username)) {
                if (empty($username)) {
                    $error .= "Bitte gib deinen Username an!<br>";
                } else if (strlen($username) < 6) {
                    $error .= "Ihr Benutzername darf nicht kürzer als 6 Zeichen Sein!<br>";
                } else if (strlen($username) > 30) {
                    $error .= "Ihr Benutzername darf nicht länger als 30 Zeichen Sein!<br>";
                } else if (!preg_match('/[A-Z]/', $username)) {
                    $error .= "Ihr Benutzername benötigt mindestens einen Grossbuchstaben!<br>";
                } else if (!preg_match('/[a-z]/', $username)) {
                    $error .= "Ihr Benutzername benötigt mindestens einen Kleinbuchstaben!<br>";
                }
            }
        }

        if (!empty($error)) {
            $message = $error;
        } else {
            // SQL-Statement erstellen
            $query = "update user set username = (?) where username = '$oldusername'";
            // SQL-Statement vorbereiten
            $stmt = $mysqli->prepare($query);
            if ($stmt === false) {
                $error .= 'prepare() failed ' . $mysqli->error . '<br />';
            }
            // Daten an das SQL-Statement binden
            if (!$stmt->bind_param('s', $username)) {
                $error .= 'bind_param() failed ' . $mysqli->error . '<br />';
            }
            // SQL-Statement ausführen
            if (!$stmt->execute()) {
                $error .= 'execute() failed ' . $mysqli->error . '<br />';
            }
    
            $stmt->close();
            header('Location:dataChangedSuccessfully.php');
        }
    }

    function validatePassword($password, $mysqli) {
        $query = "select password from user where username = ?";
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

    function validateUsername($username) {
        if ($username == $_SESSION['username']) {
            return false;
        } else {
            return true;
        }
    }

    function checkIfUsernameAlreadyExists($username, $mysqli) {
        $query = "select username from user where username = ?";
		$stmt = $mysqli->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result=$stmt->get_result();
		$user = $result->fetch_assoc();

        if (isset($user) && !empty($user)){
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
        <h1>Administrationbereich / Benutzernamen ändern</h1>
        <?php
        // Ausgabe der Fehlermeldungen
        if (!empty($error)) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        }?>
        <form action="" method="post">
            <div class="form-group">
				<label for="username">Neuer Benutzername:</label>
				<input type="text" name="username" class="form-control" id="username" maxlength="30" required value="<?php echo htmlspecialchars($username) ?>" placeholder="Gross- und Keinbuchstaben, min 6 Zeichen.">
			</div>
            <div class="form-group">
				<label for="password">Passwort:</label>
				<input type="password" name="password" class="form-control" id="password" maxlength="255" required value="<?php echo htmlspecialchars($password) ?>" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute">
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