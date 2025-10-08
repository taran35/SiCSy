<?php
require_once 'secure.php';


header('Content-Type: application/json');
require_once '../../bdd/file_bdd.php';

$parent = $_GET['parent'] ?? '/';
$name = $_GET['name'] ?? null;

if ($name === null) {
    http_response_code(400);
    echo 'error';
    exit;
}

try {
    $sql = "SELECT content FROM files WHERE parent = ? AND name = ? AND type = 'files'";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo 'erreur_mysql';
    }

    $stmt->bind_param("ss", $parent, $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if (isset($result)) {
        $res = $result->fetch_array()[0];
        echo $res;
    } else {
        echo '';
    }


    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(500);
    echo 'erreur_mysql';
}


