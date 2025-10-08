<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);


$host = 'host';
$db = 'dbname';
$user = 'user';
$pass = 'password';



$mysqli = new mysqli($host, $user, $pass, $db);
$mysqli->set_charset("utf8");
$offset = date('I') ? '+02:00' : '+01:00';
$mysqli->query("SET time_zone = '$offset'");

if ($mysqli->connect_error) {
    die("Erreur de connexion:" . $mysqli->connect_error);
    echo 'erreur_mysql';
}
