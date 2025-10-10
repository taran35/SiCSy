<?php
session_start();
require_once '../bdd/account_bdd.php';
require_once 'adm.php';

$sql = "SELECT mail FROM adm_token";
$result = $mysqli->query($sql);

$admin_infos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $mail = $row['mail'];

        $stmt = $mysqli->prepare("SELECT pseudo, created_at FROM users WHERE mail = ?");
        $stmt->bind_param("s", $mail);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $admin_infos[] = [
            'mail' => $mail,
            'pseudo' => $user['pseudo'] ?? '—',
            'created_at' => $user['created_at'] ?? null
        ];
        $stmt->close();
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
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <title>Gestion des administrateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <h1>🧑‍💻Liste des administrateurs🧑‍💻</h1>
    <table>
        <thead>
            <tr>
                <th>Email</th>
                <th>Pseudo</th>
                <th>Date d’inscription</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admin_infos as $admin): ?>
                <tr>
                    <td data-label="Mail"><?= htmlspecialchars($admin['mail']) ?></td>
                    <td data-label="Pseudo"><?= htmlspecialchars($admin['pseudo']) ?></td>
                    <td data-label="Date d'inscription"><?= $admin['created_at'] ? date('d/m/Y H:i', strtotime($admin['created_at'])) : 'Non inscrit' ?>
                    </td>
                    <td data-label="Action">
                        <form method="post" action="#" style="display:inline;" class="remove-form">
                            <input type="hidden" name="mail" value="<?= $admin['mail'] ?>">
                            <button type="submit" class="remove-btn">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
<script>


    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(form);
            const mail = formData.get('mail');

            removeAdmin(mail);

            function removeAdmin(mail) {
                fetch('remove-admin.php', {
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
                        if (response === 'success') {
                            alert("Permissions supprimés avec success!");
                            window.location.href = "gestion-admin.php";
                        } else {
                            alert("Erreur : " + response);
                        }
                    })
                    .catch(error => console.error(error));
            }

        });
    });
</script>