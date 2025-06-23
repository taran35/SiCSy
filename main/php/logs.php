<?php
require_once 'secure.php';
require_once '../fbdd.php';

$path = $_GET['path'] ?? '/';
$type = $_GET['type'] ?? null;
$content = $_GET['content'] ?? null;

if ($type === null) {
    http_response_code(400);
    echo 'error';
    exit;
}
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

try {

    $sql = "INSERT INTO logs (IP, path, content, type) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo "erreur_mysql";
    }

    $stmt->bind_param("ssss", $ip, $path, $content, $type);


    if (!$stmt->execute()) {
        echo "erreur_mysql";
    }


    if ($stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(500);
    echo 'error';
}


