<?php
session_start();

// Check if the session variables are set
if (isset($_SESSION['LOGIN_TIME']) && isset($_SESSION['AUTH_USER']) && isset($_SESSION['data']['npk'])) {

    // Check if the session duration has passed
    if (time() - $_SESSION['LOGIN_TIME'] > $_SESSION['SESSION_DURATION']) {
        // Session has expired, return a JSON response
        header("Location: ../backend/user/logout");
        exit();
    } else {
        // Update login time to extend the session (optional)
        $_SESSION['LOGIN_TIME'] = time();
    }

} else {
    // Session variables are not set, redirect to login page
    header("Location: ../");
    exit();
}