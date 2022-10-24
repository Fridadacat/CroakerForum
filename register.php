<?php
include('db_connector.inc.php');
session_start();

// Initialisierung
$error = '';
$firstname = $lastname = $email = $username = $password = $message = '';

// Wurden Daten mit "POST" gesendet?
if ($_SERVER['REQUEST_METHOD'] == "POST") {

	$firstname = trim($_POST['firstname']);
	$lastname = trim($_POST['lastname']);
	$email = trim($_POST['email']);
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (isset($firstname)) {
		if (empty($firstname)) {
			$error .= "Bitte gib deinen Voramen an!<br>";
		} else if (strlen($firstname) > 30) {
			$error .= "Ihr Vorname darf nicht länger als 30 Zeichen Sein!<br>";
		}
	}

	if (isset($lastname)) {
		if (empty($lastname)) {
			$error .= "Bitte gib deinen Nachnamen an!<br>";
		} else if (strlen($lastname) > 30) {
			$error .= "Ihr Nachnamen darf nicht länger als 30 Zeichen Sein!<br>";
		}
	}

	if (isset($email)) {
		if (empty($email)) {
			$error .= "Bitte gib deine Email an!<br>";
		} else if (strlen($email) > 100) {
			$error .= "Ihre Email darf nicht länger als 100 Zeichen Sein!<br>";
		}
	}

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

	if (isset($password)) {
		if (empty($password)) {
			$error . "Bitte gib dein Passwort an!<br>";
		} else if (strlen($password) < 8) {
			$error .= "Ihr Passwort darf nicht kürzer als 8 Zeichen Sein!<br>";
		} else if (strlen($password) > 255) {
			$error .= "Ihr Passwort darf nicht länger als 255 Zeichen Sein!<br>";
		} else if (!preg_match('/[A-Z]/', $password)) {
			$error .= "Ihr Passwort benötigt mindestens einen Grossbuchstaben!<br>";
		} else if (!preg_match('/[a-z]/', $password)) {
			$error .= "Ihr Passwort benötigt mindestens einen Kleinbuchstaben!<br>";
		} else if (!preg_match('/[0-9]/', $password)) {
			$error .= "Ihr Passwort benötigt mindestens eine Zahl!<br>";
		} else if (strpos($password, '+' || '-' || '*' || '%' || '&' || '/' || '(' || ')' || '=' || '?' || '!' || '$')) {
			$error .= "Ihr Passwort benötigt mindestens einen Sonderzeichen. Zugelassen sind: +,-,*,%,&,/,(,),=,?,!,$.<br>";
		} else if(!preg_match('/[^äöüÄÖÜ]/', $password)) {
			$error .= "Ihr Passwort beinhaltet Sonderzeichen!<br>";
		}
	}


	// keine Fehler vorhanden
	if (empty($error)) {

		$message = "Keine Fehler vorhanden";

		//$result = $mysqli->query("select count(*) from users");
		//$row = $result->fetch_row();
		//$index = $row[0]+1;

		$hashedPassword = hashPassword($password);
		if ($hashedPassword === false) {
			$error .= "Ihr passwort konnte nicht gehasht werden und ist somit nicht sicher! <br>";
			$hashedPassword = $password;
		}

		$error = sendRegistrationDataToDatabase($mysqli, $error, $firstname, $lastname, $email, $username, $hashedPassword);

		if (!empty($error)) {
			$message = $error;
		} else {
			//loginpage
			header('Location:login.php'); //redirect
		}
	} else {
		$message = $error;
	}
}

function sendRegistrationDataToDatabase($mysqli, $error, $firstname, $lastname, $email, $username, $hashedPassword)
{
	if (chekIfUserAllreadyExists($mysqli, $username)) {
		$error .= "Dieser Benutzer existiert bereits <br>";
		return $error;
	}
	// SQL-Statement erstellen
	$query = "Insert into user (firstname, lastname, email, username, password) values (?, ?, ?, ?, ?)";
	// SQL-Statement vorbereiten
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		$error .= 'prepare() failed ' . $mysqli->error . '<br />';
	}
	// Daten an das SQL-Statement binden
	if (!$stmt->bind_param('sssss', $firstname, $lastname, $email, $username, $hashedPassword)) {
		$error .= 'bind_param() failed ' . $mysqli->error . '<br />';
	}
	// SQL-Statement ausführen
	if (!$stmt->execute()) {
		$error .= 'execute() failed ' . $mysqli->error . '<br />';
	}

	$stmt->close();
	return $error;
}

function chekIfUserAllreadyExists($mysqli, $username)
{
	$query = "select * from user where username = ?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$dbPassword = $result->fetch_assoc();

	if (empty($dbPassword)) {
		return false;
	} else {
		return true;
	}
}

function hashPassword($password)
{
	return password_hash($password, PASSWORD_DEFAULT);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registrierung</title>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>

<body>
	<?php
    require __DIR__ . '/uielements.php';
    echo showNavigationBar($_SESSION);
    ?>
	<div class="container">
		<h1>Registrierung</h1>
		<p>
			Bitte registrieren Sie sich, damit Sie diesen Dienst benutzen können.
		</p>
		<?php
		// Ausgabe der Fehlermeldungen
		if (strlen($error)) {
			echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
		} elseif (strlen($message)) {
			echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
		}
		?>
		<form action="" method="post">
			<div class="form-group">
				<label for="firstname">Vorname *</label>
				<input type="text" name="firstname" class="form-control" id="firstname" maxlength="30" required value="<?php echo htmlspecialchars($firstname) ?>" placeholder="Geben Sie Ihren Vornamen an.">
			</div>
			<div class="form-group">
				<label for="lastname">Nachname *</label>
				<input type="text" name="lastname" class="form-control" id="lastname" maxlength="30" required value="<?php echo htmlspecialchars($lastname) ?>" placeholder="Geben Sie Ihren Nachnamen an">
			</div>
			<div class="form-group">
				<label for="email">Email *</label>
				<input type="email" name="email" class="form-control" id="email" maxlength="100" required value="<?php echo htmlspecialchars($email) ?>" placeholder="Geben Sie Ihre Email-Adresse an.">
			</div>
			<div class="form-group">
				<label for="username">Benutzername *</label>
				<input type="text" name="username" pattern="[0-9a-zA-Z]{6,30}" class="form-control" id="username" maxlength="30" required value="<?php echo htmlspecialchars($username) ?>" placeholder="Gross- und Keinbuchstaben, min 6 Zeichen.">
			</div>
			<div class="form-group">
				<label for="password">Password *</label>
				<input type="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" class="form-control" id="password" maxlength="255" required value="<?php echo htmlspecialchars($password) ?>" placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 8 Zeichen, keine Umlaute">
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