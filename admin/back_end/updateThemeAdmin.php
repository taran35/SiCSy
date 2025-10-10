<?php
require_once __DIR__ . '/../../main/php/secure.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Méthode non autorisée.";
    exit;
}

$theme = $_POST['theme'] ?? null;
if (!$theme) {
    echo "Aucun theme spécifié.";
    exit;
}

$config = "../../themes-admin/config.json";
$json = file_get_contents($config);
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erreur lors de la lecture du fichier JSON.";
    exit;
}

    $data['theme'] = $theme;


if (file_put_contents($config, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo "Erreur lors de l'écriture du fichier.";
    exit;
}

echo "success";
