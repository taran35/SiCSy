<?php
session_start();
require_once '../account/bdd.php';
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des administrateurs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
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
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .remove-btn {
            padding: 0.3rem 0.6rem;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .remove-btn:hover {
            background-color: #c82333;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1e1e1e;
                color: #eee;
            }

            table {
                background-color: #2a2a2a;
            }

            th,
            td {
                border-color: #444;
            }

            tr:hover {
                background-color: #333;
            }

            .remove-btn {
                background-color: #ff4f5e;
            }

            .remove-btn:hover {
                background-color: #d03a46;
            }
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
            table {
                background-color: rgb(62, 63, 65);
            }
            td::before {
                color: white;
            }
            tbody tr {
                background-color: rgb(62, 63, 65);
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
                        'X-Requested-With': '<^3i{~i5ln4(h#`s*$d]-d|;xx.s{tt#$~&2$jd{fzo|epmk+~k[;9[d/+7*b-q'
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