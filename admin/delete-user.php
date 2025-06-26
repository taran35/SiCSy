<?php
session_start();
require_once 'adm.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de compte</title>
    <link rel="stylesheet" href="base.css">
</head>

<body>
    <header>
        <div style="display:flex; justify-content: space-between; align-items:center;">
            <button onclick="window.location.href='dash.php'" id="home" aria-label="retour a la page d'accueil" style="
            background:none; 
            border:none; 
            color:white; 
            font-size:1.5rem; 
            cursor:pointer;
        ">🏠</button>
            <div>Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> 👋</div>
            <button id="theme-toggle" aria-label="Basculer le thème" style="
            background:none; 
            border:none; 
            color:white; 
            font-size:1.5rem; 
            cursor:pointer;
        ">🌙</button>
        </div>
    </header>
    <div class="container">
        <main>
            <div class="reg">
                <h1>Supprimer un utilisateur</h1>
                <hr>
                <div class="form">
                    <form id="deleteform" novalidate>
                        <div class="form-group">
                            <input type="email" id="email" placeholder="Adresse email" required />
                            <div id="emailError" class="error-message"></div>
                        <input type="submit" value="Supprimer un compte" />
                    </form>
                </div>
            </div>
        </main>
    </div>
    <footer>
        <p><a class="logout" href="logout.php">Se déconnecter</a></p>
        <p class="credits"><a class="credits2" href="https://github.com/taran35/cloud">Copyright © 2025 Taran35</a></p>
    </footer>
    <script>
        const form = document.getElementById("deleteform");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            let isValid = true;

            const email = document.getElementById("email");

            clearErrors();


            if (!validateEmail(email.value.trim())) {
                showError(email, "emailError", "Adresse email invalide.");
                isValid = false;
            }

            if (isValid) {
                accountDelete(email.value.trim());
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

        function accountDelete(mail) {
            fetch('account-delete.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
                },
                body: new URLSearchParams({
                    'email': mail
                })
            })
                .then(response => response.text())
                .then(response => {
                    if (response === 'success') {
                        alert("Compte supprimé avec succès !");
                        window.location.href = "dash.php";
                    } else {
                        alert("Erreur : " + response);
                    }
                })
                .catch(error => console.error(error));
        }
    </script>
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const currentTheme = localStorage.getItem('theme');

        if (currentTheme) {
            document.documentElement.setAttribute('data-theme', currentTheme);
            themeToggleBtn.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            themeToggleBtn.textContent = '🌙';
            localStorage.setItem('theme', 'light');
        }

        function switchTheme() {
            const theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'light');
                themeToggleBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                themeToggleBtn.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            }
        }

        themeToggleBtn.addEventListener('click', switchTheme);
    </script>


    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f5f7fa;
            align-items: center;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
            padding: 1rem;
        }

        main {
            width: 100%;
            max-width: 450px;
        }

        .container {
            display: flex;
            justify-content: center;
        }

        .reg {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
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
            width: 100%;
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
            width: 100%;
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
            text-align: left;
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
            width: 100%;
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

        @media (max-width: 480px) {
            .reg {
                padding: 20px;
                border-radius: 12px;
            }

            .reg h1 {
                font-size: 22px;
            }
        }

        [data-theme="dark"] {
            .form, .reg {
                background-color: rgb(62, 63, 65);
            }
            input[type=text], input[type=email], input[type=password] {
                background-color: rgb(142, 150, 158);
            }
        }
    </style>
</body>

</html>