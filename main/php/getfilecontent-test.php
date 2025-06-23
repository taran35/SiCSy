<?php
require_once 'secure.php';


header('Content-Type: application/json');


$parent = $_GET['parent'] ?? '/';
$name = $_GET['name'] ?? null;

if ($name === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Merci de spécifier un fichier']);
    exit;
}

echo 'Ligne 1\\nLigne 2\\nLigne 3';



