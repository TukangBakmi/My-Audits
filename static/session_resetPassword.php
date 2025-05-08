<?php
require_once '../config/dbconn.php';
// Start the session
session_start();

$header = 'Location: ./admin';
$tokenInfo = "active";

// Check if the session variables are set
if (isset($_GET['id']) && isset($_GET['role']) && isset($_GET['token'])) {

    $id = $_GET['id'];
    $role = $_GET['role'];
    $token = $_GET['token'];

    $gettoken = $conn->prepare("SELECT token FROM password_reset_tokens WHERE token = ? AND role = ?");
    $gettoken->bind_param("ss", $token, $role);
    $gettoken->execute();
    $gettoken->store_result();

    if ($gettoken->num_rows > 0) {
        date_default_timezone_set('Asia/Bangkok');
        $currentTime = strtotime(date('Y-m-d H:i:s'));

        $latestTimeQuery = "SELECT MAX(expiry_time) AS latest_expiry_time  FROM password_reset_tokens WHERE id_user = '$id' AND role = '$role' GROUP BY id_user";
        
        $query = "SELECT * FROM password_reset_tokens WHERE expiry_time = ($latestTimeQuery)";
        $getLatestToken = $conn->query($query)->fetch_assoc();

        $tokenIsUsed = $getLatestToken['used'];
        $latestToken = $getLatestToken['token'];
        $expiredTime = strtotime($getLatestToken['expiry_time']);

        if ($token != $latestToken || $currentTime > $expiredTime || $tokenIsUsed == 1) {
            // Token is expired or token is not the latest
            $tokenInfo = 'expired';
        }
    } else {
        // Token is not valid
        header($header);
        exit();
    }


} else {
    // Session variables are not set, redirect to login page
    header($header);
    exit();
}