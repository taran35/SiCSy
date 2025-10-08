<?php
require_once 'secure.php';
require_once '../../bdd/file_bdd.php';

$parent = $_GET['parent'] ?? '/';
$name = $_GET['name'] ?? null;
$path = $_GET['path'] ?? null;

if ($name === null) {
    http_response_code(400);
    echo 'error';
    exit;
}
if ($path === null) {
    http_response_code(400);
    echo 'error';
    exit;
}


try {
    $sql = "SELECT content FROM files WHERE parent = ? AND name = ? AND type = 'files'";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo 'erreur_mysql';
        exit;
    }

    $stmt->bind_param("ss", $parent, $name);
    $stmt->execute();
    $result = $stmt->get_result();
    if (isset($result)) {
        $res = $result->fetch_array()[0];
    } else {
        $res = '';
    }
$stmt->close();


} catch (Exception $e) {
    http_response_code(500);
    echo 'erreur_mysql';
    exit;
}






try {

    if ($path === null) {
        $var1 = '/';
        $var2 = null;
    } else {

        $normalized = trim($path, '/');
        $parts = explode('/', $normalized);
        $Fpath = '/' . $normalized;

        if (count($parts) >= 2) {
            $var2 = array_pop($parts);
            $var1 = '/' . implode('/', $parts);
        } elseif (count($parts) === 1 && $parts[0] !== '') {
            $var1 = '/' . $parts[0];
            $var2 = null;
        } else {
            $var1 = '/';
            $var2 = null;
        }
    }
    $temppath = $path ?? '/';
    if ($var2 == null) {
        if ($parent == $temppath) {
            echo 'no_change';
            flush();
            exit;
        }
    } else {
        $temp = $var1 . '/' . $var2;
        if ($temp == $parent) {
            echo 'no_change';
            flush();
            exit;
        }
    }


    if (is_null($var2) == false) {
        $sql = "SELECT 1 FROM files WHERE parent = ? AND name = ? AND type = 'folder' LIMIT 1";

        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo 'erreur_mysql';
            exit;
        }
        $stmt->bind_param("ss", $var1, $var2);
        if (!$stmt->execute()) {
            echo 'erreur_mysql';
            exit;
        }

        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows == 0) {
            echo 'inexistant';
        } else {

            $sql = "SELECT 1 FROM files WHERE parent = ? AND name = ? AND type = 'files' LIMIT 1";

            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo 'erreur_mysql';
                exit;
            }
            $stmt->bind_param("ss", $Fpath, $name);
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

                $stmt->bind_param("sss", $Fpath, $name, $res);


                if (!$stmt->execute()) {
                    echo 'erreur_mysql';
                    exit;
                }


                if ($stmt->affected_rows > 0) {
                    $sql = "DELETE FROM files WHERE parent = ? and name = ? AND type = 'files'";
                    $stmt = $mysqli->prepare($sql);
                    if (!$stmt) {
                        echo 'erreur_mysql';
                        exit;
                    }

                    $stmt->bind_param("ss", $parent, $name);


                    if (!$stmt->execute()) {
                        echo 'erreur_mysql';
                        exit;
                    }


                    if ($stmt->affected_rows > 0) {
                        echo "success";
                        flush();
                        exit;

                    } else {
                        echo 'error';
                        exit;
                    }
                } else {
                    echo "error";
                    exit;
                }
            } else {
                echo "name_indisp";
                flush();
                exit;

            }
        }
        $stmt->close();
    } else {
        $temp = trim($var1, '/'); // "/Docs/" -> "Docs"
        if ($temp != '') {
            $sql = "SELECT 1 FROM files WHERE parent = '/' AND name = ? AND type = 'folder' LIMIT 1";

            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo 'erreur_mysql';
                exit;
            }
            $stmt->bind_param("s", $temp);
            if (!$stmt->execute()) {
                echo 'erreur_mysql';
                exit;
            }

            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows == 0) {
                echo 'inexistant';
                flush();
                exit;
            }
        }

        $sql = "SELECT 1 FROM files WHERE parent = ? AND name = ? AND type = 'files' LIMIT 1";

        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            echo 'erreur_mysql';
            exit;
        }
        $stmt->bind_param("ss", $Fpath, $name);
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

            $stmt->bind_param("sss", $var1, $name, $res);


            if (!$stmt->execute()) {
                echo 'erreur_mysql';
                exit;
            }


            if ($stmt->affected_rows > 0) {
                $sql = "DELETE FROM files WHERE parent = ? and name = ? AND type = 'files'";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo 'erreur_mysql';
                    exit;
                }

                $stmt->bind_param("ss", $parent, $name);


                if (!$stmt->execute()) {
                    echo 'erreur_mysql';
                    exit;
                }


                if ($stmt->affected_rows > 0) {
                    echo "success";
                    flush();
                    exit;

                } else {
                    echo 'error';
                    exit;
                }
            } else {
                echo 'error';
                exit;
            }
        } else {
            echo 'name_indisp';
            flush();
            exit;
        }
    }





    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    http_response_code(500);
    echo 'error';
    exit;
}





