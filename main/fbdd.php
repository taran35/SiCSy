<?php
$host = '136.243.63.156';
$db = 's173_files_cloud';
$user = 'u173_HLF2xj4qgm';
$pass = 'ygEWQ=5!=5Nty=JZpDZYn^e7';



$mysqli = new mysqli($host, $user, $pass, $db);
$mysqli->set_charset("utf8");
if ($mysqli->connect_error) {
    die("Erreur de connexion:" . $mysqli->connect_error);
    echo 'erreur_mysql';
}
