<?php
$host = 'host';
$db = 'db';
$user = 'user';
$pass = 'pass';



$mysqli = new mysqli($host, $user, $pass, $db);
$mysqli->set_charset("utf8");
if ($mysqli->connect_error) {
    die("Erreur de connexion:" . $mysqli->connect_error);
    echo 'erreur_mysql';
}
