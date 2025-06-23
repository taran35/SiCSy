<?php
require_once 'secure.php';
session_start();
$folder = $_GET['folder'] ?? 'none';
$action = $_GET['action'] ?? 'none';
if ($action == 'none') {
    http_response_code(500);
    exit;
}
if ($action == 'back') {
    $path = $_SESSION['parent'];
    $parentPath = dirname($path);
    if ($parentPath === "\\") {
        $_SESSION['parent'] = "/";
    } else {
        $_SESSION['parent'] = $parentPath;
    }
    exit;
}
if ($action == 'up') {
    if ($folder == 'none') {
        http_response_code(500);
        exit;
    } else {
        $path = $_SESSION['parent'];
        if ($path == '/') {
            $_SESSION['parent'] = '/' . $folder;
        } else {
            $_SESSION['parent'] = $path . '/' . $folder;
        }
        exit;
    }
}
if ($action == 'get') {
    echo $_SESSION['parent'];
    exit;
}