<?php

require_once __DIR__ . '/../bdd/account_bdd.php';

    $sql = "SELECT token FROM tokens WHERE type = 'CSRF' AND info = 'login' LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo 'erreur_mysql';
        exit;
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $csrf_token = $row['token'];
    } else {
        $stmt->close();
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO tokens (type, info, token) VALUES ('CSRF', 'login', ?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo 'erreur_mysql';
            exit;
        }
        $stmt->bind_param("s",  $token);
        $stmt->execute();
        $csrf_token = $token;
    }
    $stmt->close();

$mysqli->close();
?>