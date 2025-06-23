<?php
require_once '../main/php/secure.php';
require_once './bdd.php';
$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'mail_invalide';
    exit;
}

$sql = "SELECT 1 FROM email_codes WHERE email = ? AND created_at >= NOW() - INTERVAL 10 MINUTE LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo 'code_already_exist';
    exit;
}
$stmt->close();

$code = random_int(100000, 999999);

$sql = "
    INSERT INTO email_codes (email, code, created_at)
    VALUES (?, ?, NOW())
    ON DUPLICATE KEY UPDATE code = VALUES(code), created_at = VALUES(created_at)
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("si", $email, $code);
if ($stmt->execute()) {
    echo "success";

    // ENVOIE PAR MAIL

    
} else {
    http_response_code(500);
    echo "error";
}
$stmt->close();