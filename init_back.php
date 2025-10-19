<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_exception_handler(function ($e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
});
set_error_handler(function ($errno, $errstr) {
    echo json_encode(['success' => false, 'error' => $errstr]);
    exit;
});

$data = json_decode(file_get_contents('php://input'), true);
// Test database connection
if ($data['action'] === 'test_bdd') {
    $mysqli = @new mysqli($data['host'], $data['user'], $data['pass'], $data['db']);
    echo json_encode(['success' => !$mysqli->connect_error]);
    exit;
}


// Write database config
if ($data['action'] === 'write_bdd') {
    $file = $data['file'];
    if ($file === 'file_bdd.php') {
        $filename = './bdd/file_bdd.php';
    } else if ($file === 'account_bdd.php') {
        $filename = './bdd/account_bdd.php';
    } else {
        echo json_encode(['success' => false, 'error' => 'Nom de fichier invalide.']);
        exit;
    }

    $content = file_get_contents($filename);

    if ($content === false) {
        echo json_encode(['success' => false, 'error' => 'Erreur lors de la lecture du fichier.']);
        exit;
    }

    $newHost = $data['host'];
    $newDb = $data['db'];
    $newUser = $data['user'];
    $newPass = $data['pass'];

    $content = preg_replace("/\\\$host = '.*?';/", "\\\$host = '$newHost';", $content);
    $content = preg_replace("/\\\$db = '.*?';/", "\\\$db = '$newDb';", $content);
    $content = preg_replace("/\\\$user = '.*?';/", "\\\$user = '$newUser';", $content);
    $content = preg_replace("/\\\$pass = '.*?';/", "\\\$pass = '$newPass';", $content);

    $result = file_put_contents($filename, $content);

    if ($result === false) {
        echo json_encode(['success' => false, 'error' => "Erreur lors de l'écriture dans le fichier."]);
    } else {
        echo json_encode(['success' => true]);
    }
    exit;
}



// Setup database tables
if ($data['action'] === 'setup_bdd') {
    require_once './bdd/account_bdd.php';

    $queries = [
        "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(200) NOT NULL,
    mail VARCHAR(200) NOT NULL,
    password VARCHAR(500) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP 
);",

        "CREATE TABLE email_codes (
    email VARCHAR(200) PRIMARY KEY,
    code INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP 
);",
        "CREATE TABLE adm_token (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(200) NOT NULL,
    token VARCHAR(250) NOT NULL
);",
"CREATE TABLE tokens(
  ID INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(25) NOT NULL,
  token VARCHAR(300) NOT NULL,
  info VARCHAR(500),
  date DATETIME DEFAULT CURRENT_TIMESTAMP
  );"
    ];

    foreach ($queries as $query) {
        if (!$mysqli->query($query)) {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la création des tables: ' . $mysqli->error]);
            exit;
        }
    }


    require_once './bdd/file_bdd.php';
    $queries = [
        "CREATE TABLE files (
    parent VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    content VARCHAR(10000),
    type VARCHAR(10) NOT NULL
);",

        "CREATE TABLE logs (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    IP VARCHAR(20) NOT NULL,
    type VARCHAR(30) NOT NULL,
    path VARCHAR(500) NOT NULL,
    content VARCHAR(10000),
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user VARCHAR(100) NOT NULL
);"
    ];

    foreach ($queries as $query) {
        if (!$mysqli->query($query)) {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la création des tables: ' . $mysqli->error]);
            exit;
        }
    }

    echo json_encode(['success' => true]);
}


// Register admin user
if ($data['action'] === 'register_admin') {
    require_once './bdd/account_bdd.php';



    $email = trim($data['email']);
    $password = $data['password'];
    $pseudo = trim($data['pseudo']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'mail_invalide';
        exit;
    }
    if (strlen($pseudo) < 3) {
        echo 'pseudo_len';
        exit;
    }
    if (strlen($password) < 8) {
        echo 'password_len';
        exit;
    }

    try {


        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (mail, password, pseudo) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $email, $password_hash, $pseudo);
        if (!$stmt->execute()) {
            echo 'erreur_mysql';
            exit;
        }
        

        $token = bin2hex(random_bytes(32));

        $sql = "INSERT INTO adm_token (mail, token) VALUES (?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $email, $token);
        if (!$stmt->execute()) {
            echo 'erreur_mysql';
            exit;
        }
        $stmt->close();
        $mysqli->close();
        echo "success";
        exit;

    } catch (PDOException $e) {
        echo "error";
    }

}
//etape up
if ($data['action'] === 'etape_up') {
    $configPath = './bdd/config.json';
    $config = json_decode(file_get_contents($configPath), true);
    $config['etape'] = '1';
    file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));
}

// valider setup
if ($data['action'] === 'valid_setup') {
    $configPath = './bdd/config.json';
    $config = json_decode(file_get_contents($configPath), true);
    $config['etat'] = 'true';
    file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));
}
?>