<?php
require_once 'secure.php';
require_once '../fbdd.php';

$parent = $_GET['parent'] ?? '/';
$name = $_GET['name'] ?? null;

if ($name === null) {
    http_response_code(400);
    echo 'error';
    exit;
}

try {
    $sql = "SELECT * FROM files WHERE parent = ? AND name = ? AND type = 'folder'";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo 'erreur_mysql';
    }

    $stmt->bind_param("ss", $parent, $name);

    if (!$stmt->execute()) {
        echo 'erreur_mysql';
    }

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo 'folder_already_exist';
    } else {
        $sql = "INSERT INTO files (parent, name, content, type) VALUES (?, ?, '', 'folder')";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo 'erreur_mysql';
        }

        $stmt->bind_param("ss", $parent, $name);


        if (!$stmt->execute()) {
            echo 'erreur_mysql';
        }


        if ($stmt->affected_rows > 0) {
            echo "success";
        } else {
            echo "error";
        }
    }
    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(500);
    echo 'error';
}


