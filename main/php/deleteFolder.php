<?php
require_once 'secure.php';
require_once '../fbdd.php';

$parent = $_GET['parent'] ?? null;
$name = $_GET['name'] ?? null;

if ($parent === null || $name === null) {
    http_response_code(400);
    echo 'error';
    exit();
}

try {
  $like1 = rtrim($parent, '/') . '/' . $name;
$like2 = $like1 . '/%';

$sql = "DELETE FROM files 
        WHERE (parent = ? AND name = ?)
           OR parent = ?
           OR parent LIKE ?";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo 'erreur_mysql';
    exit;
}

$stmt->bind_param("ssss", $parent, $name, $like1, $like2);

if (!$stmt->execute()) {
    echo 'erreur_mysql';
    exit;
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
