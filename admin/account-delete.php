<?php
require_once '../account/bdd.php';
require_once '../main/php/secure.php';

if (!isset($_POST['email'])) {
    echo 'champs_manquants';
    exit;
}

$email = $_POST['email'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo 'mail_invalide';
    exit;
}

try {

    $sql = "DELETE FROM users WHERE mail = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    $mysqli->close();

} catch (PDOException $e) {
    echo "error";
}
?>