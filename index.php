<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();


if (!isset($_SESSION['username'])) {
    header('Location: ./account/login.php');
    exit;
}

$username = $_SESSION['username'];

if (isset($_SESSION['parent'])) {
} else {
    $_SESSION['parent'] = '/';
}


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="./main/cloud_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/codemirror.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/codemirror.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.9/theme/monokai.min.css">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

</head>

<body>
    <div id="welcome">
        <h1>Bienvenue <span><?= htmlspecialchars($username) ?></span> !</h1>
    </div>

    <h2>Fichiers disponibles :</h2>
    <div id="buttons" class="buttons"></div>
    <div id="file-container">
    </div>

    <p><a href="account/logout.php">Se déconnecter</a></p>



</body>
<footer>
    <div class="theme-switch-wrapper">
        <label class="theme-switch" for="checkbox">
            <input type="checkbox" id="checkbox" />
            <div class="slider round"></div>
        </label>
        <em>Activer theme sombre</em>
    </div>
</footer>

<script>/*dark/light button*/

    const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
    const currentTheme = localStorage.getItem('theme');
    if (currentTheme) {
        document.documentElement.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark') {
            toggleSwitch.checked = true;
        }
    }

    function switchTheme(e) {
        if (e.target.checked) {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
        }
        else {
            document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');
        }
        const theme = localStorage.getItem('theme');
    }
    toggleSwitch.addEventListener('change', switchTheme, false);
</script>
<script>
    var Sparent = "<?php echo $_SESSION['parent']; ?>";
</script>
<script src="main/cloud_script.js"></script>

</html>