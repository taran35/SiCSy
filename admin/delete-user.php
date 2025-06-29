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
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression de compte</title>
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

    </style>
</body>

</html>