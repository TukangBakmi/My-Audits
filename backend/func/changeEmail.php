<?php
require_once '../../config/dbconn.php';
require_once '../func/cleaner.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Getting all data
    $id = $_SESSION["data"]["id"];
    $email = cleaner($_POST["email"], $conn);
    
    $role = isset($_SESSION["AUTH_USER"]) ? 'user' : 'admin';

    if (!empty($email)) {
        $getEmail = $conn->prepare("SELECT email FROM $role WHERE email = ? AND id != ?");
        $getEmail->bind_param("si", $email, $id);
        $getEmail->execute();
        
        $getEmail->store_result();
        if ($getEmail->num_rows > 0) {
            $response["status"] = "error";
            $response["title"] = "Edit Failed";
            $response["msg"] = "Email " . $email . " already registered!";
        }
        else {
            try{
                // update user
                $stmt = $conn->prepare("UPDATE $role SET email=? WHERE id=?");
                $stmt->bind_param("si", $email, $id);
                if($stmt->execute()){
                    $_SESSION["data"]["email"] = $email;
                    $response["status"] = "success";
                    $response["title"] = "Edit Success!";
                    $response["msg"] = "Email changed successfully";
                }else{
                    $response["status"] = "error";
                    $response["title"] = "Edit Failed";
                    $response["msg"] = $conn->error;
                };
                $stmt->close();
            } catch (Exception $e) {
                $conn->rollback();
                $response["status"] = "error";
                $response["title"] = "Edit Failed";
                $response["msg"] = $e->getMessage();
            }
        }
    }

    // Close the connection
    $conn->close();
}
echo json_encode($response);

?>