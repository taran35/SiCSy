<?php
require_once 'secure.php';
header('Content-Type: application/json');


$parent = $_GET['parent'] ?? '/';

if ($parent === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Merci de spécifier un dossier']);
    exit;
}

try {
    if ($parent == '/') {
        $rows = [
        [ "name"=> "rapport.js", "type"=> "file" ],
        [ "name"=> "images", "type"=> "folder" ]
        ];
    } else if ($parent == '/images') {
        $rows = [
        [ "name"=> "test.txt", "type"=> "file" ],
        [ "name"=> "test2.txt", "type"=> "file" ],
        [ "name"=> "test3.txt", "type"=> "file" ],
        [ "name"=> "folder2", "type"=> "folder" ]
        ];
    }

    if(isset($rows)) {
        echo json_encode(['content' => $rows]);
    } else {
        $rows = 'empty';
        echo json_encode(['content' => $rows]);
    }


} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
