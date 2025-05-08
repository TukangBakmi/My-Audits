<?php
// Start the session
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the login page or any other page after logout
header("Location: ../../admin");
exit(); // Ensure no more code is executed after the redirect
?>