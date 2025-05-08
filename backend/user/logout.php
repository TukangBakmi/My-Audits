<?php
require_once '../../config/dbconn.php';
// Start the session
session_start();

// update admin
$id = $_SESSION["data"]["id"];
$loggedOut = 'true';
$loggedStat = 'false';
$stmt = $conn->prepare("UPDATE visitor SET date_logout=CURRENT_TIMESTAMP, logged_out=? WHERE id_user=? AND logged_out=?");
$stmt->bind_param("sis", $loggedOut, $id, $loggedStat);
if($stmt->execute()){
    $stmt->close();
    
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or any other page after logout
    header("Location: ../../");

}else{
    $response["status"] = "error";
    $response["title"] = "Error";
    $response["msg"] = $conn->error;
};

exit(); // Ensure no more code is executed after the redirect
?>