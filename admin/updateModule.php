<?php
require_once '../main/php/secure.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = $_POST['folder'] ?? null;

    if (!$folder) {
        echo "Aucun fichier spécifié.";
        exit;
    }

    $chemin_fichier = '../modules/' . basename($folder) . '/config.json';

    if (!file_exists($chemin_fichier)) {
        echo "Fichier non trouvé : $chemin_fichier";
        exit;
    }

    $json = file_get_contents($chemin_fichier);
    $data = json_decode($json, true);

    if (isset($_POST['param']) && is_array($_POST['param'])) {
        $data['param'] = $_POST['param'];
    } else {
        $nouveau_param = [];

        foreach ($_POST as $cle => $valeur) {
            if ($cle !== 'folder') {
                $nouveau_param[$cle] = $valeur;
            }
        }

        $data['param'] = $nouveau_param;
    }
    file_put_contents($chemin_fichier, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "success";
}
?>
