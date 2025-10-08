<?php
require_once 'secure.php';
require_once '../../bdd/file_bdd.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file']) || !isset($_POST['directory'])) {
        echo "info_manquante";
        exit;
    }

    $file = $_FILES['file'];
    $path = $_POST['directory'];
    $tmpPath = $file['tmp_name'];
    $filename = $file['name'];

    if (str_ends_with($filename, '.svg') || str_ends_with($filename, '.png') || str_ends_with($filename, '.jpeg') || str_ends_with($filename, '.jpg') || str_ends_with($filename, '.webp')){
        echo 'image non acceptée';
        exit;
    }
    if(str_ends_with($filename, '.pdf')) {
        echo 'pdf non accepté';
        exit;
    }

    $contenu = file_get_contents($tmpPath);
    $Fcontenu = str_replace(["\r\n", "\n", "\r"], '\\n', $contenu);



     $sql = "SELECT 1 FROM files WHERE parent = ? AND name = ? AND type = 'files' LIMIT 1";

            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo 'erreur_mysql';
                exit;
            }
            $stmt->bind_param("ss", $path, $filename);
            if (!$stmt->execute()) {
                echo 'erreur_mysql';
                exit;
            }

            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows == 0) {




                $sql = "INSERT INTO files (parent, name, content, type) VALUES (?, ?, ?, 'files')";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo 'erreur_mysql';
                    exit;
                }

                $stmt->bind_param("sss", $path, $filename, $Fcontenu);


                if (!$stmt->execute()) {
                    echo 'erreur_mysql';
                    exit;
                }
                echo 'success';
                 $stmt->close();
            } else { 
                echo 'nom_indisponible';
                exit;
            }

}
?>
