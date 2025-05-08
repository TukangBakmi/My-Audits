<?php
require_once '../../config/dbconn.php';
require_once '../func/cleaner.php';
session_start();

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $npk = cleaner($_POST["npk"], $conn);
    $password = cleaner($_POST["password"], $conn);

    $error = array( "error_status" => false );

    if (empty($npk)) {
        $error["error_status"] = true;
        $error["npk"] = "NPK is required!";
    }
    if (empty($password)) {
        $error["error_status"] = true;
        $error["password"] = "Password is required!";
    }
    if ($error["error_status"] == true) {
        echo json_encode($error);
        exit();
    }
    $response = authenticateUser($conn, $npk, $password);
}
echo json_encode($response);

function authenticateUser($conn, $npk, $password)
{
    $response = [];
    $conn->begin_transaction();

    try {
        $queryUser = $conn->prepare("SELECT a.id, a.npk, a.nama_lengkap, a.email, a.password, b.position, a.date_joined FROM user a
                                        LEFT JOIN position b ON a.id_position = b.id WHERE a.npk = ? ");
        $queryUser->bind_param("i", $npk);
        $queryUser->execute();
        $res = $queryUser->get_result();

        if ($res->num_rows == 1) {
            $data = $res->fetch_assoc();

            if (password_verify($password, $data['password'])) {
                $idUser = $data["id"];
                $npkUser = $data["npk"];
                $name = $data["nama_lengkap"];
                $email = $data["email"];
                $position = $data["position"];
                $date = $data["date_joined"];

                $checkVisitor = $conn->prepare("SELECT * FROM visitor WHERE npk_user = ? AND logged_out = 'false' ");
                $checkVisitor->bind_param("i", $npk);
                $checkVisitor->execute();
                $res = $checkVisitor->get_result();

                if ($res->num_rows == 0) {
                    // Insert to visitor
                    $stmt = $conn->prepare("INSERT INTO visitor (id_user, npk_user, full_name, logged_out) VALUES(?, ?, ?, 'false')");
                    $stmt->bind_param("iis", $idUser, $npkUser, $name);
                    if($stmt->execute()){
                        setSessionUser($idUser, $npkUser, $name, $email, $position, $date);
        
                        $response["status"] = "success";
                        $response["title"] = "Login Success!";
                        $response["msg"] = "Welcome " . $data["nama_lengkap"];
                    }else{
                        $response["status"] = "error";
                        $response["title"] = "Error!";
                        $response["msg"] = $conn->error;
                    };
                    $stmt->close();

                    $conn->commit();
                    $queryUser->close();
                } else {
                    setSessionUser($idUser, $npkUser, $name, $email, $position, $date);
                    $response["status"] = "success";
                    $response["title"] = "Login Success!";
                    $response["msg"] = "Welcome " . $data["nama_lengkap"];
                }
            } else {
                $response["title"] = "Login Failed!";
                $response["status"] = "error";
                $response["msg"] = "Wrong password!";
            }
        } else {
            $response["title"] = "Login Failed!";
            $response["status"] = "error";
            $response["msg"] = "NPK is not registered!";
        }
    } catch (Exception $e) {
        $conn->rollback();
        $response["status"] = "error";
        $response["title"] = "Login Failed";
        $response["msg"] = $e->getMessage();
    } finally {
        $conn->close();
    }
    return $response;
}

function setSessionUser($idUser, $npkUser, $name, $email, $position, $date) {
    $_SESSION["AUTH_USER"] = true;
    $_SESSION["LOGIN_TIME"] = time();
    $_SESSION["SESSION_DURATION"] = 1800; // in seconds
    $_SESSION["data"]["id"] = $idUser;
    $_SESSION["data"]["npk"] = $npkUser;
    $_SESSION["data"]["namaLengkap"] = $name;
    $_SESSION["data"]["email"] = $email;
    $_SESSION["data"]["position"] = $position;
    $_SESSION["data"]["dateJoined"] = $date;
}