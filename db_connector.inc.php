<?php

//initialisierung datenbank
$host = 'localhost';
$username = 'croaker';
$password = 'croaker123';
$databse = 'mydb';

//mit datenbank verbinden
$mysqli = new mysqli($host, $username, $password, $databse);

// Fehlermeldung, falls Verbindung fehl schlägt.
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}

?>