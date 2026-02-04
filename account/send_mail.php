<?php

require_once __DIR__ . '/../main/php/secure.php';

if (!isset($_GET['dest'], $_GET['title'], $_GET['body'])) {
    echo 'champs_manquants';
    exit;
}


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer(true);

$dest = trim($_GET['dest']) ?? '';
$title = trim($_GET['title']) ?? '';
$body = trim($_GET['body']) ?? '';    
try {
    $mail->isSMTP();
    $mail->Host = '100.123.0.1';
    $mail->Port = 1025;
    $mail->SMTPAuth = false; //true en prod
    $mail->Username   = 'user@local.test';              
    $mail->Password   = 'secret';                       
  //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 

    $mail->setFrom('bot@local.test', 'No Reply');
    $mail->addAddress($dest);

    $mail->Subject = $title;
    $mail->Body = $body;

    $mail->send();
    echo "Mail envoyé ✅";
} catch (Exception $e) {
    echo "Erreur: " . $mail->ErrorInfo;
}