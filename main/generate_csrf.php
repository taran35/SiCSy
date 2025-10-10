<?php

require_once 'bdd/account_bdd.php';

if (empty($_SESSION['csrf_token'])) {
    $sql = "SELECT token FROM tokens WHERE type = 'CSRF' AND info = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo 'erreur_mysql';
        exit;
    }
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['csrf_token'] = $row['token'];
    } else {
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO tokens (type, info, token) VALUES ('CSRF', ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo 'erreur_mysql';
            exit;
        }
        $stmt->bind_param("ss", $_SESSION['username'], $token);
        $stmt->execute();
        $_SESSION['csrf_token'] = $token;
    }
    $stmt->close();
}
$mysqli->close();
?>