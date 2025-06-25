<?php 
$mail = '';
$pseudo = '';
$password = '';
$queries = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mail = $_POST["email"] ?? '';
    $pseudo = $_POST["pseudo"] ?? '';
    $password = $_POST["password"] ?? '';

    $token = bin2hex(random_bytes(32));
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $queries[] = "INSERT INTO users (pseudo, mail, password) VALUES ('" . $pseudo . "','" . $mail . "','" . $password_hash . "')";
    $queries[] = "INSERT INTO adm_token (mail, token) VALUES ('" . $mail . "','" . $token . "')";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Formulaire</title>
    <style>
        .query-box {
            position: relative;
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            height: 80px;
            font-family: monospace;
            font-size: 14px;
            padding: 10px;
            resize: vertical;
        }
        button.copy-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="form" id="form">
    <form action="" method="POST" id="form">
        <input type="text" id="pseudo" name="pseudo" size="30" minlength="4" placeholder="Pseudo" required />
        <input type="email" id="email" name="email" size="30" placeholder="Adresse email" required />
        <input type="password" id="password" name="password" size="30" minlength="8" placeholder="Mot De Passe" required />
        <input type="submit" value="envoyer les informations" />
    </form>
</div>

<?php if (!empty($queries)): ?>
    <h3>Requêtes SQL à exécuter :</h3>
    <?php foreach ($queries as $index => $query): ?>
        <div class="query-box">
            <textarea id="query-<?php echo $index; ?>" readonly><?php echo $query; ?></textarea>
            <button class="copy-btn" data-target="query-<?php echo $index; ?>">Copier</button>
        </div>
    <?php endforeach; ?>
    <p> N'oubliez pas de supprimer les fichiers <strong>bdd.txt</strong> et <strong>create_first_admin.php</strong> apres avoir initialiser le cloud</p>
<?php endif; ?>

<script>
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const textarea = document.getElementById(targetId);
            textarea.select();
            textarea.setSelectionRange(0, 99999); 

            try {
                document.execCommand('copy');
                alert('Requête copiée !');
            } catch (err) {
                alert('Impossible de copier');
            }

            // Désélectionner
            window.getSelection().removeAllRanges();
        });
    });
</script>

</body>
</html>
