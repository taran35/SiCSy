<?php
session_start();
require_once '../account/bdd.php';
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
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <title>Visualisation des logs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
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
    <h1>🧾 Journal des Actions 🧾</h1>
    <?php
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    $type = $_GET['type'] ?? '';
    $ip = $_GET['ip'] ?? '';
    $date_start = $_GET['date_start'] ?? '';
    $date_end = $_GET['date_end'] ?? '';
    $path = $_GET['path'] ?? '';
    $user = $_GET['user'] ?? '';

    $sql = "SELECT * FROM logs WHERE 1=1";
    $params = [];
    $types = "";

    if ($type != "") {
        $sql .= " AND type = ?";
        $params[] = $type;
        $types .= "s";
    }
    if ($ip != "") {
        $sql .= " AND IP LIKE ?";
        $params[] = "%$ip%";
        $types .= "s";
    }
    if ($user != "") {
        $sql .= " AND user LIKE ?";
        $params[] = "%$user%";
        $types .= "s";
    }
    if ($path != "") {
        $sql .= " AND path LIKE ?";
        $params[] = "%$path%";
        $types .= "s";
    }
    if ($date_start != "") {
        $sql .= " AND date >= ?";
        $params[] = $date_start;
        $types .= "s";
    }
    if ($date_end != "") {
        $sql .= " AND date <= ?";
        $params[] = $date_end . " 23:59:59";
        $types .= "s";
    }

    $sql .= " ORDER BY date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";


    $stmt = $mysqli->prepare($sql);
    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <form method="get">
        <label>Type :
            <select name="type">
                <option value="">-- Tous --</option>
                <?php
                $typesList = ['deleteFile', 'createFile', 'uploadFile', 'downloadFile', 'createFolder', 'deleteFolder', 'moveFile', 'renameFile'];
                foreach ($typesList as $t) {
                    $sel = ($type === $t) ? "selected" : "";
                    echo "<option value=\"$t\" $sel>$t</option>";
                }
                ?>
            </select>
        </label>

        <label>IP :
            <input type="text" name="ip" value="<?= htmlspecialchars($ip) ?>">
        </label>
        <label>Utilisateur :
            <input type="text" name="user" value="<?= htmlspecialchars($_GET['user'] ?? '') ?>">
        </label>
        <label>Path :
            <input type="text" name="path" value="<?= htmlspecialchars($path) ?>">
        </label>

        <label>Date début :
            <input type="date" name="date_start" value="<?= htmlspecialchars($date_start) ?>">
        </label>

        <label>Date fin :
            <input type="date" name="date_end" value="<?= htmlspecialchars($date_end) ?>">
        </label>

        <input type="submit" value="Rechercher">
    </form>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>IP</th>
                <th>Utilisateur</th>
                <th>Type</th>
                <th>Path</th>
                <th>Contenu</th>
            </tr>
        </thead>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Date"><?= htmlspecialchars($row['date']) ?></td>
                <td data-label="IP"><?= htmlspecialchars($row['IP']) ?></td>
                <td data-label="Utilisateur"><?= htmlspecialchars($row['user']) ?></td>
                <td data-label="Type">
                    <span class="type-badge type-<?= htmlspecialchars($row['type']) ?>">
                        <?= htmlspecialchars($row['type']) ?>
                    </span>
                </td>
                <td data-label="Path"><?= htmlspecialchars($row['path']) ?></td>
                <td data-label="Contenu">
                    <?php
                    $content = trim($row['content']);
                    if ($content !== "" && strtolower($content) !== "null"):
                        ?>
                        <span class="content-toggle">Voir</span>
                        <div class="content-box"><?= nl2br(htmlspecialchars($content)) ?></div>
                    <?php endif; ?>
                </td>
            </tr>

        <?php endwhile; ?>
    </table>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.content-toggle').forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const content = this.nextElementSibling;
                    const isVisible = content.style.display === 'block';
                    content.style.display = isVisible ? 'none' : 'block';
                    this.textContent = isVisible ? 'Voir' : 'Cacher';
                });
            });
        });
    </script>


    <?php
    $count_sql = "SELECT COUNT(*) FROM logs WHERE 1=1";
    $count_params = [];
    $count_types = "";
    if ($type != "") {
        $count_sql .= " AND type = ?";
        $count_params[] = $type;
        $count_types .= "s";
    }
    if ($ip != "") {
        $count_sql .= " AND IP LIKE ?";
        $count_params[] = "%$ip%";
        $count_types .= "s";
    }
    if ($user != "") {
        $count_sql .= " AND user LIKE ?";
        $count_params[] = "%$user%";
        $count_types .= "s";
    }
    if ($path != "") {
        $count_sql .= " AND path LIKE ?";
        $count_params[] = "%$path%";
        $count_types .= "s";
    }
    if ($date_start != "") {
        $count_sql .= " AND date >= ?";
        $count_params[] = $date_start;
        $count_types .= "s";
    }
    if ($date_end != "") {
        $count_sql .= " AND date <= ?";
        $count_params[] = $date_end . " 23:59:59";
        $count_types .= "s";
    }
    $count_stmt = $mysqli->prepare($count_sql);
    if (!empty($count_types)) {
        $count_stmt->bind_param($count_types, ...$count_params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_rows = $count_result->fetch_row()[0];
    $total_pages = ceil($total_rows / $limit);
    ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">← Précédent</a>
        <?php endif; ?>
        <span>Page <?= $page ?> / <?= $total_pages ?></span>
        <?php if ($page < $total_pages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Suivant →</a>
        <?php endif; ?>
    </div>

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
