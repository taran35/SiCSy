<?php
session_start();
require_once 'adm.php';
$configPath = "../themes/config.json";
$json = file_get_contents($configPath);
$data = json_decode($json, true);
$theme_actuel = $data['theme'];
?>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres des themes</title>
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
    <main>
        <div class="box1">
            <h1>⚙️Paramètres des themes⚙️</h1>
            <p> theme actuel : <strong><?php echo $theme_actuel ?></strong></p>
        </div>
        <?php


        $basePath = '../themes/';

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

                echo "<h3>Theme : $folder</h3>";

                if (file_exists($configPath)) {
                    $json = file_get_contents($configPath);
                    $data = json_decode($json, true);
                    $index = $data['index'];
                    $index_name = $data['index_name'];
                    $theme_descr = $data['theme_descr'];
                    $theme_file = $data["css_file"];
                    ?>
                    <div class="box">
                        <p style="margin-right: 20%; margin-left: 20%; text-align: center;"> <?php echo $theme_descr ?></p>
                        <form>
                            <input id="theme" name="theme" type="hidden" value="<?php echo $folder ?>">
                            <input id="file" name="file" type="hidden" value="<?php echo $theme_file ?>">
                            <div class="boutons">
                                <input type="submit" value="Choisir ce theme">
                                <?php
                                if ($index_name != "") {
                                    echo "<button id='index' name='index' class='index'
                                    onclick='window.location.href='" . $index . "'>" . $index_name . "</button>";
                                }
                                ?>
                            </div>
                        </form>
                    </div>
                    <?php


                } else {
                    echo "<p>Le fichier de configuration $configPath n'existe pas.</p>";
                    continue;
                }
            }
        }
        ?>
    </main>
    <footer>
        <p><a class="logout" href="logout.php">Se déconnecter</a></p>
        <p class="credits"><a class="credits2" href="https://github.com/taran35/cloud">Copyright © 2025 Taran35</a></p>
    </footer>
</body>
<style>
    main {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9f9fb;
        color: #333;
        margin: 0;
        padding: 2rem;
        text-align: center;
        display: grid;
        transition: background-color 0.3s, color 0.3s;
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
    }

    .box {
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

    .box1 {
        height: fit-content;
    }

    label {
        font-weight: bold;
        margin-bottom: 5px;
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
        .box {
            background-color: rgb(62, 63, 65);
        }

        input[type=text] {
            background-color: rgb(150, 152, 155);
        }

        hr {
            color: #rgb(34, 39, 49);
        }

        main {
            background-color: #1e1e1e;
        }

        label,
        h1,
        p {
            color: rgb(150, 152, 155);
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
        const themeInput = form.querySelector('#theme');
        const fileInput = form.querySelector('#file');

        fetch('updateTheme.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
            },
            body: new URLSearchParams({
                'theme': themeInput.value,
                'file': fileInput.value
            })
        }).then(response => response.text())
            .then(response => {
                if (response == "success") {
                    alert("Paramètres modifiés avec succès");
                } else {
                    alert("Une erreur est survenue : " + response);
                }
            });


    });
</script>