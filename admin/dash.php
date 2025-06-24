<?php
session_start();
require_once 'adm.php';

require_once '../account/bdd.php';

$res_logs = $mysqli->query("SELECT COUNT(*) AS total FROM logs");
$total_logs = ($res_logs && $row = $res_logs->fetch_assoc()) ? $row['total'] : 0;

$res_users = $mysqli->query("SELECT COUNT(*) AS total FROM users");
$total_users = ($res_users && $row = $res_users->fetch_assoc()) ? $row['total'] : 0;




$log_counts = [];
$log_dates = [];


$period = new DatePeriod(
    new DateTime('-7 days'),
    new DateInterval('P1D'),
    new DateTime('+0 day')
);

foreach ($period as $date) {
    $formatted = $date->format('Y-m-d');
    $log_dates[$formatted] = 0;
}

$sql = "SELECT DATE(date) AS log_date, COUNT(*) AS total 
        FROM logs 
        WHERE date >= CURDATE() - INTERVAL 7 DAY
        GROUP BY log_date";

$result = $mysqli->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $log_dates[$row['log_date']] = (int) $row['total'];
    }
}


$log_labels = array_keys($log_dates);
$log_data = array_values($log_dates);

$mysqli->close();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de bord admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }

        header {
            background-color: #007bff;
            color: white;
            padding: 1rem;
            text-align: center;
            margin: 1rem;
            border-radius: 15px;
        }

        .container {
            padding-left: 2rem;
            max-width: 70%;
            text-align: center;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-wrap: nowrap;
            gap: 1rem;
            margin: 2rem 0;
            width: fit-content;
        }

        .button-group a {
            padding: 1rem 2rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .button-group a:hover {
            background-color: #0056b3;
        }

        canvas {
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            max-width: 100%;
        }

        footer {
            padding: 1rem;
            text-align: center;
            background-color: #222;
            color: white;
            margin: 1rem;
            border-radius: 15px;
        }

        footer a {
            color: #00c4ff;
            text-decoration: none;
        }

        .box {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            width: 100%;
            padding: 10px;
        }

        .stats-box {
            width: fit-content;
            background-color: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            height: fit-content;
            width: 100%;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1e1e1e;
                color: #ddd;
            }

            canvas {
                background-color: #2a2a2a;
            }

            header,
            .button-group a {
                background-color: #0d6efd;
            }

            footer {
                background-color: #111;
            }

            .stats-box {
                background-color: #2a2a2a;
                color: #fff;
            }
        }

        .box2, .box3 {
            display: flex;
            flex-direction: column;
            margin: 20px;
            padding: 7px;
            text-align: center;
            align-items: center;
        }
    </style>
</head>

<body>

    <header>
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h1>
    </header>
    <div class="box">
        <div class="box2">
            <div class="stats-box">
                <h3>📊 Statistiques</h3>
                <ul style="list-style: none; padding-left: 0;">
                    <li><strong>Logs totaux :</strong> <?= $total_logs ?></li>
                    <li><strong>Utilisateurs :</strong> <?= $total_users ?></li>
                </ul>
            </div>
            <div class="button-group">
                <a href="logs.php">Voir les logs</a>
                <a href="register.php">Ajouter un utilisateur</a>
                <a href="users.php">Gérer les utilisateurs</a>
            </div>
        </div>

        <div class="container">
            <h2>Activité du cloud sur les 7 derniers jours</h2>
            <canvas id="logChart" width="600" height="300"></canvas>
        </div>
        <div class="box3">
            <div class="button-group">
                <a href="gestion-admin.php">Gerer les admins</a>
                <a href="add-admin.php">Ajouter un admin</a>
            </div>
        </div>
    </div>

    <footer>
        <p><a href="logout.php">Se déconnecter</a></p>
    </footer>

    <script>
        const labels = <?php echo json_encode($log_labels); ?>;
        const data = <?php echo json_encode($log_data); ?>;

        const ctx = document.getElementById('logChart').getContext('2d');
        const logChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de logs',
                    data: data,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.3,
                    pointRadius: 5,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Logs'
                        },
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    </script>


</body>

</html>