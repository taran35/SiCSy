<?php
require_once '../bdd/account_bdd.php';
require_once '../main/php/secure.php';

if (!isset($_POST['mail'])) {
    echo 'champs_manquants';
    exit;
}
$token = bin2hex(random_bytes(32));
$email = trim($_POST['mail']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'mail_invalide';
    exit;
}


$sql = "SELECT 1 FROM users WHERE mail = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
if ($result->num_rows == 1) {
    $sql = "INSERT INTO adm_token (mail, token) VALUES (?,?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }


} else {
    echo "utilisateur_inexistant";
    exit;
}