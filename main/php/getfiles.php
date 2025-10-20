<?php
require_once 'secure.php';

header('Content-Type: application/json');
require_once '../../bdd/file_bdd.php';

$parent = $_GET['parent'] ?? '/';

if ($parent === null) {
    http_response_code(400);
    echo 'error';
    exit;
}

try {

    $sql = "SELECT name, type FROM files WHERE parent = ?";

    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {

        echo'erreur_mysql';

    }



    $stmt->bind_param("s", $parent);

    $stmt->execute();

    $result = $stmt->get_result();

    $rows = [];

    while ($row = $result->fetch_assoc()) {

        $rows[] = $row;

    }

    if (isset($rows)) {

        echo json_encode(['content' => $rows]);

    } else {

        $rows = 'empty';

        echo json_encode(['content' => $rows]);

    }



    $stmt->close();

    $mysqli->close();


} catch (Exception $e) {
    http_response_code(500);
    echo 'error';
}
