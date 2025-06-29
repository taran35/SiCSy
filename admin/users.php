<?php
session_start();
require_once '../account/bdd.php';
require_once 'adm.php';

$sql = "SELECT id, pseudo, mail, created_at FROM users ORDER BY created_at DESC";
$result = $mysqli->query($sql);

$admin_mails = [];
$res_admins = $mysqli->query("SELECT mail FROM adm_token");
if ($res_admins) {
    while ($row = $res_admins->fetch_assoc()) {
        $admin_mails[] = $row['mail'];
    }
}
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
    <title>Liste des utilisateurs</title>
    <link rel='stylesheet' href='<?php echo $base ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='<?php echo $theme ?>'>
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

    <h1>🧑‍💻Utilisateurs enregistrés🧑‍💻</h1>

    <input type="text" id="search" placeholder="Rechercher un pseudo...">

    <table id="userTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pseudo</th>
                <th>Email</th>
                <th>Date d’inscription</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td data-label="ID"><?= $row['id'] ?></td>
                    <td data-label="Pseudo">
                        <?= htmlspecialchars($row['pseudo']) ?>
                        <?php if (in_array($row['mail'], $admin_mails)): ?>
                            <span class="admin-badge">Admin</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Email"><span class="mail-hidden"><?= htmlspecialchars($row['mail']) ?></span></td>
                    <td data-label="Date d’inscription"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                </tr>

            <?php endwhile; ?>
        </tbody>
    </table>

    <script>
        const search = document.getElementById('search');
        const rows = document.querySelectorAll('#userTable tbody tr');

        search.addEventListener('input', function () {
            const term = this.value.toLowerCase();
            rows.forEach(row => {
                const pseudo = row.children[1].textContent.toLowerCase();
                row.style.display = pseudo.includes(term) ? '' : 'none';
            });
        });
    </script>

    <footer>
        <p><a class="logout" href="logout.php">Se déconnecter</a></p>
        <p class="credits"><a class="credits2" href="https://github.com/taran35/cloud">Copyright © 2025 Taran35</a></p>
    </footer>
</body>
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

</html>