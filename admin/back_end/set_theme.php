<?php

$configPath = __DIR__ . "/../../themes-admin/config.json";
$json = file_get_contents($configPath);
$data = json_decode($json, true);
$fenetre = basename($_SERVER['SCRIPT_FILENAME']);
$folder = $data['theme'];

$configPath2 = __DIR__ . "/../../themes-admin/" . $folder . "/config.json";
$json2 = file_get_contents($configPath2);
$data2 = json_decode($json2, true);
$file = $data2[$fenetre];
$basePath = $data2['base'];
$theme = "/themes-admin/" . $folder . "/" . $file;
$base = "/themes-admin/" . $folder . "/" . $basePath;

?>