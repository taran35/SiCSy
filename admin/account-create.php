<?php
require_once '../account/bdd.php';
require_once '../main/php/secure.php';

if (!isset($_POST['email'], $_POST['pass'], $_POST['pseudo'])) {
    echo 'champs_manquants';
    exit;
}

$email = trim($_POST['email']);
$password = $_POST['pass'];
$pseudo = trim($_POST['pseudo']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'mail_invalide';
    exit;
}
if (strlen($pseudo) < 3) {
    echo 'pseudo_len';
    exit;
}
if (strlen($password) < 8) {
    echo 'password_len';
    exit;
}

try {

    $sql = "SELECT 1 FROM users WHERE mail = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows == 1) {
        echo "mail_already_used";
        exit;
    } else {


        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (mail, password, pseudo) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $email, $password_hash, $pseudo);
        if (!$stmt->execute()) {
            echo 'erreur_mysql';
            exit;
        }
        echo "success";
        $stmt->close();
    }
} catch (PDOException $e) {
    echo "error";
}
?>