<?php

//initialisierung datenbank
$host = 'localhost';
$username = 'webseite';
$password = 'webseite123';
$databse = '151_users';

//mit datenbank verbinden
$mysqli = new mysqli($host, $username, $password, $databse);

// Fehlermeldung, falls Verbindung fehl schlägt.
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
}

?>