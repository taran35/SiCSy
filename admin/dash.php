<?php
session_start();
require_once 'adm.php';
require_once '../account/bdd.php';


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
    <link rel='stylesheet' href='<?php echo $theme ?>'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="https://taran35.github.io/SiCSy-website/wiki.html?page=modules">Voir les modules disponibles</a>
                <hr style="width: 80%;">
                <a href="theme.php">Gérer les themes</a>
                <a href="https://taran35.github.io/SiCSy-website/wiki.html?page=themes">Voir les themes disponibles</a>
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
                <hr style="width: 80%;">
                <a href="theme-admin.php">Gérer les themes admin</a>
                <a href="https://taran35.github.io/SiCSy-website/wiki.html?page=themes-admin">Voir les themes du panel administrateur disponibles</a>
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