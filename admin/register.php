<?php
session_start();
require_once 'adm.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Création de compte</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        .reg {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .reg h1 {
            margin-bottom: 20px;
            font-size: 26px;
        }

        .reg hr {
            border: none;
            border-top: 1.5px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 14px;
        }

        .form input[type="text"],
        .form input[type="email"],
        .form input[type="password"] {
            padding: 12px 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f9f9f9;
            transition: border-color 0.3s;
        }

        .form input:focus {
            border-color: #4285f4;
            outline: none;
            background-color: #fff;
        }

        .form input.error {
            border-color: #e74c3c;
            background-color: #ffe9e9;
        }

        .error-message {
            font-size: 13px;
            color: red;
            margin-top: 4px;
            margin-bottom: 6px;
        }

        input[type="submit"] {
            padding: 12px 16px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #2c6fe2;
        }

        .login {
            margin-top: 20px;
            font-size: 14px;
        }

        .login a {
            color: #4285f4;
            text-decoration: none;
            font-weight: bold;
        }

        .login a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <main>
        <div class="reg">
            <h1>Ajouter un utilisateur</h1>
            <hr>
            <div class="form">
                <form id="registerform" novalidate>
                    <div class="form-group">
                        <input type="text" id="pseudo" placeholder="Pseudo" required />
                        <div id="pseudoError" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <input type="email" id="email" placeholder="Adresse email" required />
                        <div id="emailError" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password" placeholder="Mot de passe" required minlength="8" />
                        <div id="passwordError" class="error-message"></div>
                    </div>

                    <div class="form-group">
                        <input type="password" id="password2" placeholder="Vérifier le mot de passe" required
                            minlength="8" />
                        <div id="password2Error" class="error-message"></div>
                    </div>

                    <input type="submit" value="Créer un compte" />
                </form>
            </div>
        </div>
    </main>

    <script>
        const form = document.getElementById("registerform");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            let isValid = true;

            const pseudo = document.getElementById("pseudo");
            const email = document.getElementById("email");
            const password = document.getElementById("password");
            const password2 = document.getElementById("password2");

            clearErrors();

            if (pseudo.value.trim() === "") {
                showError(pseudo, "pseudoError", "Le pseudo est requis.");
                isValid = false;
            }

            if (!validateEmail(email.value.trim())) {
                showError(email, "emailError", "Adresse email invalide.");
                isValid = false;
            }

            if (password.value.length < 8) {
                showError(password, "passwordError", "Le mot de passe doit contenir au moins 8 caractères.");
                isValid = false;
            }

            if (password.value !== password2.value) {
                showError(password2, "password2Error", "Les mots de passe ne correspondent pas.");
                isValid = false;
            }

            if (isValid) {
                accountCreate(email.value.trim(), password.value.trim(), pseudo.value.trim());
            }
        });

        function showError(input, errorId, message) {
            input.classList.add("error");
            document.getElementById(errorId).textContent = message;
        }

        function clearErrors() {
            const inputs = document.querySelectorAll("input");
            const errors = document.querySelectorAll(".error-message");

            inputs.forEach(i => i.classList.remove("error"));
            errors.forEach(e => e.textContent = "");
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function accountCreate(mail, password, pseudo) {
            fetch('account-create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                },
                body: new URLSearchParams({
                    'email': mail,
                    'pass': password,
                    'pseudo': pseudo
                })
            })
                .then(response => response.text())
                .then(response => {
                    if (response === 'success') {
                        alert("Compte créé avec succès !");
                        window.location.href = "./login.php";
                    } else {
                        alert("Erreur : " + response);
                    }
                })
                .catch(error => console.error(error));
        }
    </script>
</body>

</html>