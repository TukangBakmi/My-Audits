<?php

require_once '../../config/dbconn.php';
require_once '../../vendor/autoload.php';
require_once '../func/cleaner.php';
session_start();

$uploadDir = '../../assets/uploads/';
$response = [];

$response["count"]["total"] = 0;
$response["count"]["success"] = 0;
$response["count"]["error"] = 0;
$response["count"]["new"] = 0;
$response["count"]["old"] = 0;

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['download']) && $_GET['download'] === 'true') {
        
        // File Name
        $fileName = "File.xlsx";
        // Header in Excel
        $column = array("Id", "Nama File", "Raw File Size", "Formatted File Size", "Diunggah oleh", "Tanggal diunggah");
        // Excel Data
        $sql = "SELECT id, nama, size, FORMAT(size, 2) as formatted_size, uploaded_by, date_uploaded FROM file";

        // Fetch data from the database
        $result = $conn->query($sql);
    
        // Create a new Excel object
        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Add Header
        $row = 1;
        $col = 1;
        foreach ($column as $value){
            $sheet->setCellValueByColumnAndRow($col, $row, $value);
            $col++;
        }

        // Add data to the Excel sheet
        $row++;
        while ($row_data = $result->fetch_assoc()) {
            $row_data["formatted_size"] = formatSizeUnits($row_data["size"]);
            $col = 1;
            foreach ($row_data as $value) {
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }
    
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
    
        // Save Excel file to PHP output
        $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
    
    else if (isset($_GET['type']) && $_GET['type'] === 'details'){
        $id = cleaner($_GET['id'], $conn);
        $name = cleaner($_GET['name'], $conn);

        try {
            $file = $conn->query("SELECT * FROM file WHERE id = $id")->fetch_assoc();
            $shared = $conn->query("SELECT * FROM shared_file a LEFT JOIN user b ON a.id_user = b.id WHERE a.nama_file = '$name'");
            $response["title"] = "More Details";
            $response["msg"]["name"] = $file["nama"];
            $response["msg"]["size"] = formatSizeUnits($file["size"]);
            $response["msg"]["uploader"] = $file["uploaded_by"];
            $response["msg"]["date"] = $file["date_uploaded"];
            // Fetch all rows into an array
            $sharedData = array();
            while ($row = $shared->fetch_assoc()) {
                $sharedData[] = [
                    'nama_lengkap' => $row['nama_lengkap'] ?? 'Everyone',
                    'npk' => isset($row['npk']) ? '(' . $row['npk'] . ')' : ''
                ];
            }
            $response["msg"]["shared"] = $sharedData;
            $response["status"] = "details";
        } catch (Exception $e) {
            $response["status"] = "error";
            $response["title"] = "Something Went Wrong";
            $response["msg"] = $e->getMessage();
        }
    }
    
    else {
        $query = "SELECT *, FORMAT(size, 2) as formatted_size FROM file";
        if (isset($_SESSION["AUTH_USER"])) {
            $userId = $_SESSION["data"]["id"];
            $query = "SELECT a.id, a.nama, a.size, a.directory, a.uploaded_by, a.date_uploaded, FORMAT(size, 2) as formatted_size
                    FROM file a LEFT JOIN shared_file b ON a.nama = b.nama_file WHERE b.id_user = 'everyone' OR b.id_user = '$userId'";
        }
        $result = $conn->query($query);
        if (!$result) {
            $response["msg"] = $conn->error;
            $response["status"] = "error";
        } else {
            while ($row = $result->fetch_assoc()) {
                $row["formatted_size"] = formatSizeUnits($row["size"]);
                $response["data"][] = $row;
            }
            $response["msg"] = "Success";
            $response["status"] = "success";
        }
    }
    
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = cleaner($_POST["type"], $conn);

    // Edit
    if($type == 'edit'){
        try{
            // Edit file
            $id = cleaner($_POST['id'], $conn);
            $name = cleaner($_POST['name'], $conn);
            $newName = cleaner($_POST['newName'], $conn);

            // Check if file name exists in the database
            $sqlCheck = "SELECT nama FROM file WHERE nama = '$newName' AND NOT id = '$id'";
            $result = $conn->query($sqlCheck);

            if ($result->num_rows > 0) {
                $response["status"] = "error";
                $response["title"] = "Edit Failed";
                $response["msg"] = "File name already exists ";
            } else{
                // Construct the full file paths
                $currentFilePath = $uploadDir . $name;
                $newFilePath = $uploadDir . $newName;
                
                // Update file table
                $stmt1 = $conn->prepare("UPDATE file SET nama=?, directory=? WHERE id = ?");
                $stmt1->bind_param("ssi", $newName, $newFilePath, $id);
                // Update shared_file table
                $stmt2 = $conn->prepare("UPDATE shared_file SET nama_file=? WHERE nama_file=?");
                $stmt2->bind_param("ss", $newName, $name);

                if($stmt1->execute() && $stmt2->execute()){
                    if(rename($currentFilePath, $newFilePath)){
                        $response["status"] = "success";
                        $response["title"] = "Edit Success!";
                        $response["msg"] = "Nama file telah berhasil diganti!";
                    } else{
                        $response["status"] = "error";
                        $response["title"] = "Edit Failed";
                        $response["msg"] = $conn->error;
                    }
                }else{
                    $response["status"] = "error";
                    $response["title"] = "Edit Failed";
                    $response["msg"] = $conn->error;
                };
                $stmt1->close();
                $stmt2->close();
            }
        } catch (Exception $e) {
            $conn->rollback();
            $response["status"] = "error";
            $response["title"] = "Edit Failed";
            $response["msg"] = $e->getMessage();
        }
    }

    // Delete with button
    if($type == 'delete'){
        try{
            // Delete file
            $id = cleaner($_POST['id'], $conn);
            $filePath = cleaner($_POST['dir'], $conn);
            $fileName = cleaner($_POST['name'], $conn);

            // Delete from table file
            $stmt1 = $conn->prepare("DELETE FROM file WHERE id = ?");
            $stmt1->bind_param("i", $id);
            // Delete from table shared_file
            $stmt2 = $conn->prepare("DELETE FROM shared_file WHERE nama_file = ?");
            $stmt2->bind_param("s", $fileName);

            if($stmt1->execute() && $stmt2->execute()){
                
                if(unlink($filePath)){
                    $response["status"] = "success";
                    $response["title"] = "Delete Success!";
                    $response["msg"] = "File telah berhasil dihapus!";
                } else{
                    $response["status"] = "error";
                    $response["title"] = "Delete Failed";
                    $response["msg"] = $conn->error;
                }
            }else{
                $response["status"] = "error";
                $response["title"] = "Delete Failed";
                $response["msg"] = $conn->error;
            };
            $stmt1->close();
            $stmt2->close();
        } catch (Exception $e) {
            $conn->rollback();
            $response["status"] = "error";
            $response["title"] = "Delete Failed";
            $response["msg"] = $e->getMessage();
        }
    }

    // Check existing file name
    if($type == 'check'){
        $file = $_FILES['excel_file'];
        $idUser = $_POST['selectedValue'];

        if (empty($file['name'][0])) {
            $response["status"] = "error";
            $response["title"] = "Upload Error";
            $response["msg"] = "No files were selected.";
        }
        else if ($idUser == null) {
            $response["status"] = "error";
            $response["title"] = "Upload Error";
            $response["msg"] = "No users were selected.";
        }
        else {

            $filesName = [];
            
            // Check file exists
            foreach ($file['tmp_name'] as $key => $tmp_name) {
                $fileName = $file['name'][$key];

                // Check if file name exists in the database
                $sqlCheck = "SELECT nama FROM file WHERE nama = '$fileName'";
                $result = $conn->query($sqlCheck);

                if ($result->num_rows > 0) {
                    // name exists, confirm with user before update
                    $response["count"]["old"]++;
                    $filesName[] = $fileName;
                }
            }

            if (!empty($filesName)) {
                $oldCount = $response["count"]["old"];
                // Display SweetAlert confirmation only if there are updates
                $response["msg"] = "The destination has $oldCount files with the same names";
                $response["status"] = "confirmation";
            } else{
                // name doesn't exist, insert a new record
                $response["msg"] = "Success";
                $response["status"] = "insert all";
            }
        }
    }

    // Update all file
    if($type == 'update'){
        uploadData(true);
        successMessage();
    }

    // Insert only new file
    if($type == 'insert'){
        uploadData(false);
        successMessage();
    }
}

$conn->close();
echo json_encode($response);

function successMessage(){
    global $response;
    $response["status"] = "success";
    $response["title"] = "Upload Success";
    $response["msg"] = "<b class='mb-4'>" . $response['count']['total'] . " Total files attempted to upload</b><br><p style='color: #09b800;' class='m-0'>" . 
                        $response['count']['success'] . " files uploaded successfully<br>(" . 
                        $response['count']['new'] . " new file, " . $response['count']['old'] . " replaced file)</p><p style='color: #cc0000;'>" . 
                        $response['count']['error'] . " files failed to upload.";
}

function formatSizeUnits($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

function uploadData($update){
    global $uploadDir, $response, $conn;
    
    $file = $_FILES['excel_file'];
    $idUsers = explode(',', $_POST['selectedValue']);

    foreach ($file['tmp_name'] as $key => $tmp_name) {
        $response["count"]["total"]++;
        $fileName = $file['name'][$key];
        $fileSize = $file['size'][$key];
        $filePath = $uploadDir . basename($fileName);
        $uploader = $_SESSION["data"]['namaLengkap'] . ' (' . $_SESSION["data"]['npk'] . ')';

        // Check if file name exists in the database
        $sqlCheck = "SELECT nama FROM file WHERE nama = '$fileName'";
        $result = $conn->query($sqlCheck);


        if ($result->num_rows > 0) {
            // if user wants to update existing files
            if($update){
                if (move_uploaded_file($tmp_name, $filePath)) {
                    
                    $stmt = $conn->prepare("UPDATE file SET nama=?, directory=?, size=?, uploaded_by=?, date_uploaded=CURRENT_TIMESTAMP WHERE nama=?");
                    $stmt->bind_param("sssss", $fileName, $filePath, $fileSize, $uploader, $fileName);
                    
                    if ($stmt->execute()) {
                        insertPeopleShared($idUsers, $fileName);
                        $response["count"]["success"]++;
                        $response["count"]["old"]++;
                    } else {
                        unlink($filePath);
                        $response["count"]["error"]++;
                    }
                    
                    $stmt->close();
                } else {
                    unlink($filePath);
                    $response["count"]["error"]++;
                }
            }
        } else {
            // name doesn't exist, insert a new record
            if (move_uploaded_file($tmp_name, $filePath)) {
                
                $stmt = $conn->prepare("INSERT INTO file (nama, directory, size, uploaded_by) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $fileName, $filePath, $fileSize, $uploader);

                if ($stmt->execute()) {
                    insertPeopleShared($idUsers, $fileName);
                    $response["count"]["success"]++;
                    $response["count"]["new"]++;
                } else {
                    unlink($filePath);
                    $response["count"]["error"]++;
                }
            
                $stmt->close();
            } else {
                unlink($filePath);
                $response["count"]["error"]++;
            }
        }
    }
}

function insertPeopleShared($idUsers, $fileName) {
    global $conn;
    
    $sql = "DELETE FROM shared_file WHERE nama_file = '$fileName'";
    $conn->query($sql);

    foreach ($idUsers as $idUser) {
        // Jika akses everyone, hapus semua akses user dan ganti menjadi everyone
        if ($idUser == 'everyone') {

            $sql = "DELETE FROM shared_file WHERE nama_file = '$fileName'";
            $conn->query($sql);
            
            $sql = "INSERT INTO shared_file (nama_file, id_user) VALUES ('$fileName', '$idUser')";
            $conn->query($sql);

            break;
        }
        // Jika akses untuk user tertentu, hapus akses everyone
        else {
            $sql = "INSERT INTO shared_file (nama_file, id_user) VALUES ('$fileName', '$idUser')";
            $conn->query($sql);
        }
    }
    if (!in_array('everyone', $idUsers)) {
        $sql = "DELETE FROM shared_file WHERE nama_file = '$fileName' AND id_user = 'everyone'";
        $conn->query($sql);
    }
}
?>