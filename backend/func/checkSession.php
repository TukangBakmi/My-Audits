<?php
session_start();

$response = [];

// Check if the session variables are set
if (isset($_SESSION['LOGIN_TIME']) && $_SESSION['SESSION_DURATION'] && isset($_SESSION['data']['npk']) &&
    (isset($_SESSION['AUTH_USER']) || isset($_SESSION['AUTH_ADMIN']))) {

    // Check if the session duration has passed
    if (time() - $_SESSION['LOGIN_TIME'] > $_SESSION['SESSION_DURATION']) {
        // Session has expired, return a JSON response
        $response['status'] = 'expired';
    } else {
        // Return a JSON response indicating session is active
        $response['status'] = 'active';
    }
} else {
    // Session variables are not set, return a JSON response
    $response['status'] = 'expired';
}
echo json_encode($response);
exit();
?>