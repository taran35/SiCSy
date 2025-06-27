<?php
session_start();
require_once 'adm.php';

?>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres des modules</title>
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
    <h1>⚙️Paramètres des modules⚙️</h1>
    <?php


    $basePath = '../modules/';

    if (!is_dir($basePath)) {
        die("Erreur : le dossier '$basePath' n'existe pas.");
    }

    $folders = scandir($basePath);
    foreach ($folders as $folder) {
        if ($folder === '.' || $folder === '..')
            continue;

        $folderPath = $basePath . $folder;

        if (is_dir($folderPath)) {
            $configPath = $folderPath . '/config.json';

            echo "<h3>Module : $folder</h3>";

            if (file_exists($configPath)) {
                $json = file_get_contents($configPath);
                $data = json_decode($json, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $index = $data['index'];
                    $index_name = $data['index_name'];
                    $param = $data['param'];
                    ?>
                    <form id="<?php echo $folder ?>Form">
                        <input id="folder" name="folder" type="hidden" value="<?php echo $folder ?>" />
                        <?php
                        foreach ($param as $key => $value) {
                            echo '<div class="form-field">';
                            echo '<label for="' . $key . '">' . ucfirst($key) . ':</label>';
                            echo '<input type="text" id="' . $key . '" name="param[' . $key . ']" value="' . $value . '">';
                            echo '</div>';
                        }
                        ?>
                        <div class="boutons">
                            <input type="submit" value="Envoyer"> <button id="index" name="index" class="index"
                                onclick="window.location.href='<?php echo $index ?>'"><?php echo $index_name ?></button>
                        </div>
                    </form>
                    <?php
                } else {
                    echo "Erreur : le fichier config.json n'est pas un JSON valide.<br>";
                }
            } else {
                echo "Erreur : config.json est manquant dans $folder.<br>";
            }

            echo "<hr>";
        }
    }
    ?>
    <footer>
        <p><a class="logout" href="logout.php">Se déconnecter</a></p>
        <p class="credits"><a class="credits2" href="https://github.com/taran35/cloud">Copyright © 2025 Taran35</a></p>
    </footer>
</body>



<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f9fb;
        color: #333;
        margin: 0;
        padding: 2rem;
        text-align: center;
        display: grid;
    }

    h3 {
        color: #007acc;
        margin-top: 2rem;
        border-bottom: 2px solid #007acc;
        padding-bottom: 0.3rem;
    }

    form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        text-align: left;
        width: 100%;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    input[type="text"] {
        padding: 0.6rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        transition: border 0.3s;
    }

    input[type="text"]:focus {
        border-color: #007acc;
        outline: none;
    }

    .boutons {
        grid-column: 1 / -1;
    }

    input[type="submit"] {
        justify-self: center;
        margin-top: 1rem;
        background-color: #007acc;
        color: white;
        border: none;
        padding: 0.7rem 1.5rem;
        font-size: 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .index {
        justify-self: center;
        margin-top: 1rem;
        background-color: rgb(14, 129, 66);
        color: white;
        border: none;
        padding: 0.7rem 1.5rem;
        font-size: 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    input[type="submit"]:hover {
        background-color: #005f99;
    }

    .index:hover {
        background-color: rgb(15, 126, 196);
    }

    hr {
        margin: 3rem 0;
        border: none;
        border-top: 1px solid #ccc;
    }

    @media (max-width: 600px) {
        body {
            padding: 1rem;
        }

        form {
            padding: 1rem;
        }
    }

    [data-theme="dark"] {
        form {
            background-color: rgb(62, 63, 65);
        }

        input[type=text] {
            background-color: rgb(150, 152, 155);
        }

        hr {
            color: #rgb(34, 39, 49);
        }
    }
</style>




</html>
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

<script>
    document.addEventListener('submit', function (event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);


        fetch('updateModule.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            },
        }).then(response => response.text())
            .then(response => {
                if (response == "success") {
                    alert("Paramètres modifiés avec succès");
                } else {
                    alert("Une erreur est survenu");
                }
            });
    });
</script>