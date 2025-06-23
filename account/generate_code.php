<?php
require_once '../../main/php/secure.php';
require_once './bdd.php';
$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email invalide']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM email_codes WHERE email = ? AND created_at >= NOW() - INTERVAL 10 MINUTE");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo json_encode(['message' => 'Un code est déjà actif']);
    exit;
}

$code = random_int(100000, 999999);

$stmt = $pdo->prepare("
    INSERT INTO email_codes (email, code, created_at)
    VALUES (?, ?, NOW())
    ON DUPLICATE KEY UPDATE code = VALUES(code), created_at = VALUES(created_at)
");

$stmt->execute([$email, $code]);
echo $code;