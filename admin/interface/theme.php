<?php
session_start();
require_once __DIR__ . '/../back_end/adm.php';
require_once __DIR__ . '/../back_end/set_theme.php';

$configPath3 = "../../themes-admin/config.json";
$json3 = file_get_contents($configPath3);
$data3 = json_decode($json3, true);
$fenetre = basename(__FILE__);
$folder = $data3['theme'];



?>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <title>Paramètres des themes</title>
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
    <main>
        <div class="box1">
            <h1>⚙️Paramètres des themes⚙️</h1>
            <p> theme actuel : <strong><?php echo $theme_actuel ?></strong></p>
        </div>
        <?php


        $basePath = '../../themes/';

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
                                    echo '<button type="button" id="index" name="index" class="index" onclick="window.location.href=\'' . $index . '\'">' . $index_name . '</button>';

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

        fetch('../back_end/updateTheme.php', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo $_SESSION['csrf_token']; ?>'
            },
            body: new URLSearchParams({
                'theme': themeInput.value,
                'file': fileInput.value
            })
        }).then(response => response.text())
            .then(response => {
                if (response == "success") {
                    alert("Paramètres modifiés avec succès");
                    location.reload();
                } else {
                    alert("Une erreur est survenue : " + response);
                }
            });


    });
</script>