<?php
require_once 'secure.php';


header('Content-Type: application/json');
require_once '../fbdd.php';

$parent = $_GET['parent'] ?? '/';
$name = $_GET['name'] ?? null;
$content = $_GET['content'] ?? '';

if ($name === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Merci de spécifier un fichier']);
    exit;
}

try {
    $sql = "UPDATE files SET content = ? WHERE parent = ? AND name = ? AND type = 'files'";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception('Erreur de préparation de la requête SQL : ' . $mysqli->error);
    }

    $stmt->bind_param("sss", $content, $parent, $name);

    if (!$stmt->execute()) {
        throw new Exception('Erreur lors de l\'exécution de la requête : ' . $stmt->error);
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
    echo json_encode(['error' => $e->getMessage()]);
}


