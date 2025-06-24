<?php
$password = '12345678'; //8 caractères minimum
$mail = 'test@test.fr';
$pseudo = "test"; // 3 caractères minimum

$token = bin2hex(random_bytes(32));
$password_hash = password_hash($password, PASSWORD_DEFAULT);


echo nl2br("Voici les différentes requetes sql a faire pour initialiser le premier compte administrateur: \n \n INSERT INTO users (pseudo, mail, password) VALUES ('" . $pseudo . "','" . $mail . "','" . $password_hash . "') \n \n INSERT INTO adm_token (mail, token) VALUES ('" . $mail . "','" . $token . "')");