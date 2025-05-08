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
        $queryAdmin = $conn->prepare("SELECT a.id, npk, nama_lengkap, email, password, level FROM admin a
                                    LEFT JOIN admin_level b ON a.id_level = b.id WHERE npk = ? ");
        $queryAdmin->bind_param("i", $npk);
        $queryAdmin->execute();
        $res = $queryAdmin->get_result();

        if ($res->num_rows == 1) {
            $data = $res->fetch_assoc();

            if (password_verify($password, $data["password"])) {
                $_SESSION["AUTH_ADMIN"] = true;
                $_SESSION["LOGIN_TIME"] = time();
                $_SESSION["SESSION_DURATION"] = 1800; // in seconds
                $_SESSION["data"]["id"] = $data["id"];
                $_SESSION["data"]["npk"] = $data["npk"];
                $_SESSION["data"]["namaLengkap"] = $data["nama_lengkap"];
                $_SESSION["data"]["email"] = $data["email"];
                $_SESSION["data"]["level"] = $data["level"];

                $response["status"] = "success";
                $response["title"] = "Login Success!";
                $response["level"] = $data["level"];
                $response["msg"] = "Welcome " . $data["nama_lengkap"];

                $conn->commit();
                $queryAdmin->close();
            } else {
                $response["title"] = "Login Failed!";
                $response["status"] = "error";
                $response["msg"] = "Wrong credentials!";
            }
        } else {
            $response["title"] = "Login Failed!";
            $response["status"] = "error";
            $response["msg"] = "Wrong credentials!";
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
