<?php
require_once '../main/php/secure.php';
require_once './bdd.php';
date_default_timezone_set('Europe/Paris');
$email = $_POST['email'] ?? '';
$code = $_POST['code'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'mail_invalide';
    exit;
}
if (!isset($code)) {
    http_response_code(400);
    echo 'code_invalide';
    exit;
}

$sql = "SELECT 1 FROM email_codes WHERE email = ? AND code = ? AND created_at >= NOW() - INTERVAL 10 MINUTE LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("si", $email, $code);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    echo"code_bon";
} else {
    echo "code_invalide";
}