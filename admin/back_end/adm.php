<?php
require_once __DIR__ . '/../../bdd/account_bdd.php';


if (isset($_SESSION['adm_token'])) {
    $token = $_SESSION['adm_token'];
    $sql = "SELECT * FROM adm_token WHERE token = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows == 0) {
        header('Location: ../interface/login.php');
        exit;
    } 

} else {
    header('Location: ../interface/login.php');
    exit;
}
