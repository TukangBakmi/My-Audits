<?php

require_once '../../config/dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Delete with checkbox
    $ids = $_POST["selectedIds"];
    $dirs = $_POST["selectedDirs"];
    $names = $_POST["selectedNames"];
    $deletedRows = 0;

    for ($i = 0; $i < count($ids); $i++) {

        // Perform the DELETE query for each ID in table file
        $stmt1 = $conn->prepare("DELETE FROM file WHERE id = ?");
        $stmt1->bind_param("i", $ids[$i]);

        // Perform the DELETE query for each name in table shared_file
        $stmt2 = $conn->prepare("DELETE FROM shared_file WHERE nama_file = ?");
        $stmt2->bind_param("s", $names[$i]);

        if ($stmt1->execute() && $stmt2->execute()) {
            unlink($dirs[$i]);
            $deletedRows++;
        } else {
            $response["status"] = "error";
            $response["title"] = "Delete Failed";
            $response["msg"] = "Error deleting file with ID $ids[$i]";
            exit();
        }
        $stmt1->close();
        $stmt2->close();
    }

    $response["status"] = "success";
    $response["title"] = "Delete Success!";
    $response["msg"] = "$deletedRows Files have been successfully deleted";
}

$conn->close();
echo json_encode($response);
?>