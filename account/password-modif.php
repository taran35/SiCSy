<?php
require_once '../main/php/secure.php';
require_once './bdd.php';
$email = $_POST['email'] ?? '';
$password = $_POST['password'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'mail_invalide';
    exit;
}


if(!isset($password) || empty($password)) {
    http_response_code(400);
    echo 'password_invalide';
    exit;
}
if (strlen($password) > 8) {
    http_response_code(400);
    echo 'password_len';
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $sql = "UPDATE users SET password = ? WHERE email = ? LIMIT 1 ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $password_hash, $email);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo"success";
    } else {
        http_response_code(400);
        echo "erreur_mysql";
        exit;
    }
    $stmt->close();
}