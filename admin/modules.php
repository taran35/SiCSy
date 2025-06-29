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
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <title>Paramètres des modules</title>
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
                    $status = $data['status'] ?? 'off';
                    ?>
                    <form id="<?php echo $folder ?>Form">
                        <input id="folder" name="folder" type="hidden" value="<?php echo $folder ?>" />

                        <div class="form-field">
                            <label for="status">Status:</label>
                            <label class="switch">
                                <input type="checkbox" id="status" name="param[status]" <?php echo ($status === 'on') ? 'checked' : '' ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>

                        <?php
                        foreach ($param as $key => $value) {
                            echo '<div class="form-field">';
                            echo '<label for="' . $key . '">' . ucfirst($key) . ':</label>';
                            echo '<input type="text" id="' . $key . '" name="param[' . $key . ']" value="' . htmlspecialchars($value) . '">';
                            echo '</div>';
                        }
                        ?>

                        <div class="boutons">
                            <input type="submit" value="Sauvegarder">
                            <?php
                                if ($index_name != "") {
                                echo '<button type="button" id="index" name="index" class="index" onclick="window.location.href=\'' . $index . '\'">' . $index_name . '</button>';

                            }
                                ?>
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

        const statusCheckbox = form.querySelector('input[name="param[status]"]');
        if (statusCheckbox && !statusCheckbox.checked) {
            let hiddenInputParam = form.querySelector('input[name="param[status]"][type="hidden"]');
            if (hiddenInputParam) hiddenInputParam.remove();

            let hiddenInput = form.querySelector('input[name="status"][type="hidden"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'status';
                form.appendChild(hiddenInput);
            }
            hiddenInput.value = 'off';
        } else if (statusCheckbox && statusCheckbox.checked) {
            let hiddenInput = form.querySelector('input[name="status"][type="hidden"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'status';
                form.appendChild(hiddenInput);
            }
            hiddenInput.value = 'on';
        }

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
                    alert("Une erreur est survenue : " + response);
                }
            });
    });


</script>