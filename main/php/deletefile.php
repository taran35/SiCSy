<?php


require_once 'secure.php';
require_once '../fbdd.php';
$parent = $_GET['parent'] ?? '/';

if (isset($_GET['name'])) {
    $name = $_GET['name'];
} else {
    http_response_code(404);
    echo 'error';
    exit();
}

try {
    $sql = "DELETE FROM files WHERE parent = ? AND name = ? AND type = 'files' LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $parent, $name);
    if (!$stmt->execute()) {
        echo 'erreur_mysql';
    }

    if ($stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    echo 'error';
}