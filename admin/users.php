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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs</title>
    <link rel="stylesheet" href="base.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
            color: #333;
            padding: 2rem;
        }

        h1 {
            text-align: center;
        }

        input[type="text"] {
            display: block;
            margin: 1rem auto 2rem auto;
            padding: 0.5rem 1rem;
            width: 50%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .mail-hidden {
            color: transparent;
            transition: color 0.3s;
            cursor: pointer;
        }

        .mail-hidden:hover {
            color: #007bff;
        }

        .admin-badge {
            background-color: #28a745;
            color: white;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }



        [data-theme="dark"] {

            tr:hover {
                background-color: rgb(95, 92, 92);
            }

            th,
            td {
                padding: 12px 15px;
                text-align: left;
                border-bottom: 1px solid rgb(8, 7, 7);
            }

            table, tbody, tr {
                background-color: rgb(62, 63, 65);
            }

            input[type=text] {
                background-color: rgb(95, 92, 92);
            }
            td::before {
                color: white;
            }
            

        }

        @media screen and (max-width: 768px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
                width: 100%;
            }

            thead tr {
                display: none;
            }

            tbody tr {
                margin-bottom: 1rem;
                border: 1px solid #ccc;
                border-radius: 8px;
                padding: 1rem;
                background-color: white;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            }

            tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border: none;
                border-bottom: 1px solid #eee;
            }

            tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                color: #555;
                width: 50%;
                display: inline-block;
            }

            .admin-badge {
                margin-top: 0.5rem;
                display: inline-block;
            }
        }
    </style>
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