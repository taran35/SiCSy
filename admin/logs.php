<?php
session_start();
require_once '../account/bdd.php';
require_once 'adm.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation des logs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="base.css">
</head>

<body>
    <header>
        <div style="display:flex; justify-content: space-between; align-items:center;">
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







<style>
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: "Segoe UI", sans-serif;
        background-color: #f0f2f5;
        color: #333;
        padding: 20px;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 28px;
        color: #222;
    }

    form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        align-items: end;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    form label {
        display: flex;
        flex-direction: column;
        flex: 1 1 200px;
        font-weight: bold;
        color: #444;
    }

    form input[type="text"],
    form input[type="date"],
    form select {
        padding: 8px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    input[type="submit"] {
        padding: 12px 20px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 24px;
        font-weight: bold;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #eaeaea;
    }

    th {
        background-color: #f8f9fa;
        color: #555;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    .type-badge {
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-block;
        font-size: 13px;
        color: white;
    }

    .type-deleteFile {
        background-color: #dc3545;
    }

    .type-createFile {
        background-color: #17a2b8;
    }

    .type-uploadFile {
        background-color: #007bff;
    }

    .type-downloadFile {
        background-color: #6f42c1;
    }

    .type-createFolder {
        background-color: #28a745;
    }

    .type-deleteFolder {
        background-color: #e83e8c;
    }

    .type-moveFile {
        background-color: #fd7e14;
    }

    .type-renameFile {
        background-color: #20c997;
    }

    .type-updateFile {
        background-color: rgb(201, 198, 32);
    }

    .content-toggle {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
    }

    .content-box {
        display: none;
        white-space: pre-wrap;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        margin-top: 6px;
        font-size: 14px;
    }

    .pagination {
        text-align: center;
        margin-top: 20px;
    }

    .pagination a {
        margin: 0 5px;
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
    }

    [data-theme="dark"] {
        table {
            background-color: rgb(61, 61, 61);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgb(8, 7, 7);
        }

        td::before {
            color: white;
        }

        th, tr {
            background-color: rgb(62, 63, 65);
            color: rgb(194, 200, 201);
        }

        tr:hover {
            background-color: rgb(95, 92, 92);
        }

        form {
            background-color: rgb(61, 61, 61);
        }

        form input,
        form select {
            background-color: rgb(142, 150, 158);
        }

        form label {
            color: white;
        }

        h1 {
            color: rgb(225, 228, 231);
        }

        input[type="submit"] {
            background-color: rgb(40, 163, 67);
        }

        input[type="submit"]:hover {
            background-color: rgb(36, 131, 57);
        }



    }

    @media (max-width: 768px) {
        form {
            flex-direction: column;
            gap: 10px;
        }

        form label {
            width: 100%;
        }

        input[type="submit"] {
            width: 100%;
        }

        header>div {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .pagination {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        table,
        thead,
        tbody,
        th,
        td,
        tr {
            display: block;
        }

        thead {
            display: none;
        }

        tr {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #fff;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        td {
            padding: 8px;
            text-align: left;
            position: relative;
            border: none;
            border-bottom: 1px solid #eee;
        }

        td::before {
            content: attr(data-label);
            font-weight: bold;
            display: block;
            color: #555;
            margin-bottom: 5px;
        }

        td:last-child {
            border-bottom: none;
        }

        .content-box {
            white-space: normal;
        }

        thead {
            display: none;
        }
    }
</style>