<?php
session_start();
require_once 'adm.php';
$configPath = "../themes-admin/config.json";
$json = file_get_contents($configPath);
$data = json_decode($json, true);
$fenetre = basename(__FILE__);
$folder = $data['theme'];

$configPath2 = "../themes-admin/" . $folder . "/config.json";
$json2 = file_get_contents($configPath2);
$data2 = json_decode($json2, true);
$file = $data2[$fenetre];
$basePath = $data2['base'];
$theme = "../themes-admin/" . $folder . "/" . $file;
$base = "../themes-admin/" . $folder . "/" . $basePath;



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <title>Création de compte</title>
    <link rel='stylesheet' href='<?php echo $base ?>'>
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
    </div>
    <footer>
        <p><a class="logout" href="logout.php">Se déconnecter</a></p>
        <p class="credits"><a class="credits2" href="https://github.com/taran35/cloud">Copyright © 2025 Taran35</a></p>
    </footer>
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
                        window.location.href = "./dash.php";
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


</body>

</html>