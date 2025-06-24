<?php
ini_set('session.gc_maxlifetime', 1800);
session_start();
require_once '../account/bdd.php';
require_once '../main/php/secure.php';

$mail = trim($_POST['mail']);
$pass = $_POST['pass'];
if (!isset($mail) || !isset($pass)) {
    http_response_code(400);
    echo 'formulaire_non_rempli';
    exit();
}
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'email_invalide';
    exit();
}
$sql = "SELECT pseudo, password FROM users WHERE mail = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $mail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
if ($result->num_rows == 1) {
    if (!password_verify($pass, $row['password'])) {
        http_response_code(400);
        echo 'mot_de_passe_incorrect';
        $mysqli->close();
        exit();
    }


    $sql = "SELECT token FROM adm_token WHERE mail = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows == 1) {
        if ($row2 = $result->fetch_assoc()) {
            $_SESSION['adm_token'] = $row2['token'];
                $_SESSION['username'] = $row['pseudo'];
            echo ('success');
        } else {
            http_response_code(400);
            echo 'erreur';
            $mysqli->close();
            exit();
        }
    } else {
        http_response_code(400);
        echo 'compte_admin_inexistant';
        $mysqli->close();
        exit();
    }
} else {
    http_response_code(400);
    echo 'compte_inexistant';
    $mysqli->close();
    exit();
}
$mysqli->close();