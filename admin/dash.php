<?php
session_start();
require_once 'adm.php';
require_once '../account/bdd.php';

$res_files= $mysqli->query("SELECT COUNT(*) AS total FROM files WHERE type='files'");
$total_files = ($res_files && $row = $res_files->fetch_assoc()) ? $row['total'] : 0;

$res_users = $mysqli->query("SELECT COUNT(*) AS total FROM users");
$total_users = ($res_users && $row = $res_users->fetch_assoc()) ? $row['total'] : 0;

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
    <meta charset="UTF-8" />
    <title>Tableau de bord admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="base.css">
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 2rem;
            align-items: start;
            min-height: 60vh;
        }

        .stats-box {
            background: white;
            border-radius: 12px;
            padding: 1.5rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .stats-card {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            font-weight: 600;
            font-size: 1.3rem;
            color: #007bff;
        }

        .stats-card .icon {
            font-size: 2.8rem;
            line-height: 1;
        }

        .stats-card span.value {
            font-size: 2rem;
            color: #222;
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        .button-group a {
            padding: 1rem 1.5rem;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .button-group a:hover {
            background-color: #0056b3;
            transform: scale(1.05);
            box-shadow: 0 6px 18px rgba(0, 86, 179, 0.7);
        }

        .chart-container {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.15);
        }

        .chart-container h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.6rem;
            color: #0056b3;
            text-align: center;
        }

        canvas {
            max-width: 100%;
            border-radius: 12px;
        }



        [data-theme="dark"] {


            .stats-box,
            .chart-container {
                background-color: #2a2a2a;
                color: #ddd;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.6);
            }

            .stats-card {
                color: #3399ff;
            }

            .stats-card span.value {
                color: #eee;
            }

            .button-group a {
                background-color: #0d6efd;
                box-shadow: 0 2px 6px rgba(13, 110, 253, 0.6);
            }

            .button-group a:hover {
                background-color: #084ecc;
                box-shadow: 0 6px 18px rgba(8, 78, 204, 0.9);
            }


        }

        @media (max-width: 900px) {
            .dashboard {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
    </style>
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



    <div class="dashboard">
        <section class="stats-box">
            <div class="stats-card">
                <div class="icon">📊</div>
                <div>Fichiers totaux : <span class="value"><?= $total_files ?></span></div>
            </div>
            <div class="stats-card">
                <div class="icon">👥</div>
                <div>Utilisateurs : <span class="value"><?= $total_users ?></span></div>
            </div>
            <hr style="width: 80%;">
            <div class="button-group" style="margin-top: 2rem;">
                <a href="logs.php">Voir les logs</a>
                <hr style="width: 80%;">
                <a href="modules.php">Gérer les modules</a>
                <a href="https://github.com/taran35/cloud/blob/main/modules.md">Voir les modules disponibles</a>
                <hr style="width: 80%;">
                <a href="themes.php">Gérer les themes</a>
                <a href="https://github.com/taran35/cloud/blob/main/themes.md">Voir les themes disponibles</a>
            </div>
        </section>

        <section class="chart-container">
            <h2>Activité du cloud sur les 7 derniers jours</h2>
            <canvas id="logChart" width="600" height="300"></canvas>
        </section>

        <section class="stats-box">
            <div class="button-group" style="margin-top: 2rem;">
                <a href="gestion-admin.php">Gérer les admins</a>
                <a href="add-admin.php">Ajouter un admin</a>
                <hr style="width: 80%;">
            </div>
            <div class="button-group" style="margin-top: 2rem;">
                <a href="register.php">Ajouter un utilisateur</a>
                <a href="users.php">Voir les utilisateurs</a>
                <a href="delete-user.php">Retirer un utilisateur</a>
            </div>
        </section>
    </div>

    <footer>
        <p><a class="logout" href="logout.php">Se déconnecter</a></p>
        <p class="credits"><a class="credits2" href="https://github.com/taran35/cloud">Copyright © 2025 Taran35</a></p>
    </footer>

    <script>
        const labels = <?php echo json_encode($log_labels); ?>;
        const data = <?php echo json_encode($log_data); ?>;

        const ctx = document.getElementById('logChart').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(0,123,255,0.5)');
        gradient.addColorStop(1, 'rgba(0,123,255,0)');

        const logChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de logs',
                    data: data,
                    borderColor: '#007bff',
                    backgroundColor: gradient,
                    tension: 0.3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    borderWidth: 3,
                    hoverBorderWidth: 4,
                    hoverBackgroundColor: '#0056b3',
                    cubicInterpolationMode: 'monotone',
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'nearest',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        backgroundColor: '#007bff',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        cornerRadius: 6,
                        padding: 10,
                    },
                    legend: {
                        display: true,
                        labels: {
                            color: '#007bff',
                            font: {
                                weight: '600',
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            color: '#0056b3',
                            font: { weight: '600', size: 14 }
                        },
                        ticks: {
                            color: '#0056b3',
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Logs',
                            color: '#0056b3',
                            font: { weight: '600', size: 14 }
                        },
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#0056b3'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    </script>
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