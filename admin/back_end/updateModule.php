<?php
require_once __DIR__ . '/../../main/php/secure.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Méthode non autorisée.";
    exit;
}

$folder = $_POST['folder'] ?? null;
if (!$folder) {
    echo "Aucun dossier spécifié.";
    exit;
}

$folder = basename($folder);
$chemin_fichier = '../../modules/' . $folder . '/config.json';

if (!file_exists($chemin_fichier)) {
    echo "Fichier non trouvé : $chemin_fichier";
    exit;
}

$json = file_get_contents($chemin_fichier);
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erreur lors de la lecture du fichier JSON.";
    exit;
}

if (isset($_POST['status'])) {
    $data['status'] = ($_POST['status'] === 'on') ? 'on' : 'off';
} else {
    $data['status'] = 'off';
}

if (isset($_POST['param']) && is_array($_POST['param'])) {
    foreach ($_POST['param'] as $key => $value) {
        if ($key !== 'status') {
            $data['param'][$key] = $value;
        }
    }
}

if (file_put_contents($chemin_fichier, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo "Erreur lors de l'écriture du fichier.";
    exit;
}

echo "success";
