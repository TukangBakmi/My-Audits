<?php

require_once '../../config/dbconn.php';
require_once '../func/cleaner.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Getting all data
    $id = cleaner($_POST["id"], $conn);
    $password = cleaner($_POST["password"], $conn);
    $role = cleaner($_POST["role"], $conn);
    $continue = true;

    if (isset($_POST["token"])) {
        $token = cleaner($_POST["token"], $conn);
        $stmt = $conn->prepare("UPDATE password_reset_tokens SET used=1, date_used=NOW() WHERE token=?");
        $stmt->bind_param("s", $token);
        if($stmt->execute()){
            $continue = true;
        } else {
            $continue = false;
        }
        $stmt->close();
    };

    if ($continue) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try{
            // update admin
            $stmt = $conn->prepare("UPDATE $role SET password=? WHERE id=?");
            $stmt->bind_param("si", $hashed_password, $id);
            if($stmt->execute()){
                $response["status"] = "success";
                $response["title"] = "Edit Success!";
                $response["msg"] = "Password changed successfully";
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
    } else {
        $response["status"] = "error";
        $response["title"] = "Oops";
        $response["msg"] = "Something went wrong!";
    }
    
    // Close the connection
    $conn->close();
}
echo json_encode($response);

?>