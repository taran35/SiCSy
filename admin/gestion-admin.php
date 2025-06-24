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
    </style>
</head>

<body>
    <h1>Liste des administrateurs</h1>
<p> s'auto supprimer fait tout buguer ! </p>
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
                    <td><?= htmlspecialchars($admin['mail']) ?></td>
                    <td><?= htmlspecialchars($admin['pseudo']) ?></td>
                    <td><?= $admin['created_at'] ? date('d/m/Y H:i', strtotime($admin['created_at'])) : 'Non inscrit' ?>
                    </td>
                    <td>
                        <form method="post" action="#" style="display:inline;" class="remove-form">
                            <input type="hidden" name="mail" value="<?= $admin['mail'] ?>">
                            <button type="submit" class="remove-btn">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

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