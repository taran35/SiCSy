<?php
session_start();
require_once '../account/bdd.php';
require_once '../main/php/secure.php';
if (!isset($_POST['mail'])) {
    http_response_code(400);
    echo 'mail_non_defini';
    exit;
}

$mail = $_POST['mail'];
$sql = "DELETE FROM adm_token WHERE mail = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $mail);
$stmt->execute();
if ($stmt->affected_rows == 1) {
    echo "success";
} else {
    echo "error_mysqli: ";
}

$stmt->close();
$mysqli->close();