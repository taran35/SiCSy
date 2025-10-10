<?php
session_start();

$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_SERVER['HTTP_X_Csrf_Token'] ?? null);

if (!isset($csrfToken) || !isset($_SESSION['csrf_token']) || $csrfToken !== $_SESSION['csrf_token']) {
    http_response_code(403);
    exit('CSRF token invalide');
}




