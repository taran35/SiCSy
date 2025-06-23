<?php
require_once '../bdd.php';
require_once '../../main/php/secure.php';
function respond($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

if (!isset($_POST['email'], $_POST['pass'], $_POST['pseudo'])) {
    respond(false, 'Champs manquants');
}

$email = trim($_POST['email']);
$password = $_POST['pass']; 
$pseudo = trim($_POST['pseudo']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Email invalide');
}
if (strlen($pseudo) < 3) {
    respond(false, 'Pseudo trop court');
}
if (strlen($password) < 8) {
    respond(false, 'Mot de passe trop court (minimum 6 caractères)');
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        respond(false, 'Email déjà utilisé');
    }

    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password, pseudo) VALUES (?, ?, ?)");
    $stmt->execute([$email, $password_hash, $pseudo]);

    respond(true, 'Compte créé avec succès');
} catch (PDOException $e) {
    respond(false, 'Erreur base de données : ' . $e->getMessage());
}
?>
