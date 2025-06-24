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
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        th, td {
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

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1e1e1e;
                color: #ddd;
            }

            table {
                background-color: #2a2a2a;
            }

            th, td {
                border-color: #444;
            }

            tr:hover {
                background-color: #333;
            }

            .mail-hidden:hover {
                color: #4aa3ff;
            }

            .admin-badge {
                background-color: #2ecc71;
            }
        }
    </style>
</head>
<body>

<h1>Utilisateurs enregistrés</h1>

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
                <td><?= $row['id'] ?></td>
                <td>
                    <?= htmlspecialchars($row['pseudo']) ?>
                    <?php if (in_array($row['mail'], $admin_mails)): ?>
                        <span class="admin-badge">Admin</span>
                    <?php endif; ?>
                </td>
                <td><span class="mail-hidden"><?= htmlspecialchars($row['mail']) ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
    const search = document.getElementById('search');
    const rows = document.querySelectorAll('#userTable tbody tr');

    search.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        rows.forEach(row => {
            const pseudo = row.children[1].textContent.toLowerCase();
            row.style.display = pseudo.includes(term) ? '' : 'none';
        });
    });
</script>

</body>
</html>
