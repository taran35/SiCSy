<?php
session_start();
require_once 'adm.php';

$configPath = "../themes-admin/config.json";
$json = file_get_contents($configPath);
$data = json_decode($json, true);
$fenetre = basename(__FILE__);
$folder = $data['theme'];

$configPath2 = "../themes-admin/" . $folder ."/config.json";
$json2 = file_get_contents($configPath2);
$data2 = json_decode($json2, true);
$file = $data2[$fenetre];
$basePath = $data2['base'];
$theme = "../themes-admin/" . $folder . "/" . $file;
$base = "../themes-admin/" . $folder . "/" . $basePath;



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un administrateur</title>
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
            <div class="log">
                <h1> ajouter un administrateur</h1>
                <hr>
                <br><br>
                <div class="form">
                <form action="#" method="POST" id="form">
                    <div class="form-group">
                        <input type="email" id="email" size="30" placeholder="Adresse email" required />
                        <div id="emailError" class="error-message"></div>
                    </div>
                    <input type="submit" value="Ajouter" />
                    <div id="globalError" class="error-message" style="text-align: center;"></div>
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



<script>
    document.getElementById("form").addEventListener("submit", function (e) {
        e.preventDefault();
        clearErrors();

        const mail = document.getElementById("email").value.trim();
        let hasError = false;

        if (mail === "") {
            showError("emailError", "L'adresse email est requise");
            document.getElementById("email").classList.add("error");
            hasError = true;
        }
        if (!validateEmail(email.value)) {
            showError("emailError", "L'adresse email est incorrecte");
            document.getElementById("email").classList.add("error");
            hasError = true;
        }

        if (!hasError) {
            addAdmin(mail);
        }
    });
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    function addAdmin(mail) {
        fetch('admin-add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': $_SESSION['csrf_token'] 
            },
            body: new URLSearchParams({
                'mail': mail
            })
        })
            .then(response => response.text())
            .then(response => {
                if (response == "success") {
                    window.location.replace("gestion-admin.php");
                } else {
                    showError("globalError", "Email incorrect.");
                    console.log(response);
                }
            })
            .catch(error => {
                showError("globalError", "Erreur de connexion au serveur.");
                console.error(error);
            });
    }

    function showError(id, message) {
        document.getElementById(id).textContent = message;
    }

    function clearErrors() {
        document.querySelectorAll(".error-message").forEach(el => el.textContent = "");
        document.querySelectorAll("input").forEach(el => el.classList.remove("error"));
    }
</script>