<?php

require_once '../../config/dbconn.php';
require_once '../../vendor/autoload.php';
require_once '../func/cleaner.php';
session_start();

$response = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate file path and file ID
    $filePath = cleaner($_POST['filePath'], $conn);
    $fileId = cleaner($_POST['fileId'], $conn);

    $file = $conn->query("SELECT * FROM file WHERE id = $fileId")->fetch_assoc();

    // Log download information into the database
    if(isset($_SESSION["AUTH_USER"])) {
        $stmt = $conn->prepare("INSERT INTO log_download (id_file, file_name, file_size, id_user, npk_user, full_name, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiiiss", $fileId, $file['nama'], $file['size'], $_SESSION["data"]['id'], $_SESSION["data"]['npk'], $_SESSION["data"]['namaLengkap'], $_SESSION["data"]['position']);
    } else {
        $position = "Admin";
        $stmt = $conn->prepare("INSERT INTO log_download (id_file, file_name, file_size, id_user, npk_user, full_name, position) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isiiiss", $fileId, $file['nama'], $file['size'], $_SESSION["data"]['id'], $_SESSION["data"]['npk'], $_SESSION["data"]['namaLengkap'], $position);
    }
    if ($stmt->execute()) {
        // Set appropriate headers for file download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    } else {
        // Handle download failure (if needed)
        header("HTTP/1.1 500 Internal Server Error");
        exit("Download failed: " . $conn->error);
    }
    $stmt->close();

    // Close the database connection
    $conn->close();
} else {
    // Handle invalid request method (if needed)
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid request method.");
}
?>