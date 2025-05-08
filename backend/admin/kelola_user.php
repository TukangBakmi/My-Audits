<?php

require_once '../../config/dbconn.php';
require_once '../../vendor/autoload.php';
require_once '../func/cleaner.php';


$response = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['download']) && $_GET['download'] === 'true') {

        // File Name
        $fileName = "User.xlsx";
        // Header in Excel
        $column = array("NPK", "Nama Lengkap", "Jabatan", "Tanggal Bergabung");
        // Excel Data
        $sql = "SELECT a.npk, a.nama_lengkap, b.position, a.date_joined
                FROM user a LEFT JOIN position b ON a.id_position = b.id";

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

    else {
        $type = $_GET['type'];

        if ($type == 'user') {
            $result = $conn->query("SELECT a.id, a.npk, a.nama_lengkap, a.email, a.password, a.id_position, b.position, a.date_joined
                                    FROM user a LEFT JOIN position b ON a.id_position = b.id");
            if (!$result) {
                $response["msg"] = $conn->error;
                $response["status"] = "error";
                $conn->close();
            } else {
                while($row=$result->fetch_assoc()){
                    $response["data"][] = $row;
                }
                $response["status"] = "OK";
                $response["msg"] = "Success";
                $conn->close();
            }
        }

        if ($type == 'user_record') {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM user WHERE id = $id");
            if (!$result) {
                $response["msg"] = $conn->error;
                $response["status"] = "error";
                $conn->close();
            } else {
                while($row=$result->fetch_assoc()){
                    $response["data"][] = $row;
                }
                $response["status"] = "OK";
                $response["msg"] = "Success";
                $conn->close();
            }
        }

        if ($type == 'position') {
            $result = $conn->query("SELECT * FROM position");
            if (!$result) {
                $response["msg"] = $conn->error;
                $response["status"] = "error";
                $conn->close();
            } else {
                while($row=$result->fetch_assoc()){
                    $response["data"][] = $row;
                }
                $response["status"] = "OK";
                $response["msg"] = "Success";
                $conn->close();
            }
        }
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $type = cleaner($_POST["type"], $conn);

    if($type == 'delete'){
        try{
            // delete user
            $id = cleaner($_POST['id'], $conn);
            $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute()){
                $response["status"] = "success";
                $response["title"] = "Delete Success!";
                $response["msg"] = "User has been successfully deleted!";
            }else{
                $response["status"] = "error";
                $response["title"] = "Delete Failed";
                $response["msg"] = $conn->error;
            };
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            $response["status"] = "error";
            $response["title"] = "Delete Failed";
            $response["msg"] = $e->getMessage();
        }
    }


    if($type == 'edit' || $type == 'add'){
        // Getting all data
        $npk = cleaner($_POST["npk"], $conn);
        $name = cleaner($_POST["name"], $conn);
        $email = cleaner($_POST["email"], $conn);
        $password = cleaner($_POST["password"], $conn);
        $idPosition = cleaner($_POST["position"], $conn);
        $id = cleaner($_POST["id"], $conn);

        // Validate
        $error = array("error_status" => false);

        if (empty($npk)) {
            $error["error_status"] = true;
            $error["npk"] = "NPK is required!";
        }
        if (!empty($npk)) {
            $getNpk = $conn->prepare("SELECT npk FROM user WHERE npk = ? AND id != ?");
            $getNpk->bind_param("ii", $npk, $id);
            $getNpk->execute();
            $getNpk->store_result();
            if ($getNpk->num_rows > 0) {
                $error["error_status"] = true;
                $error["npk"] = "NPK " . $npk . " already registered!";
            }
        }
        if (empty($name)) {
            $error["error_status"] = true;
            $error["name"] = "Name is required!";
        }
        if($type == 'add'){
            if (empty($email)) {
                $error["error_status"] = true;
                $error["email"] = "Email is required!";
            }
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error["error_status"] = true;
                    $error["email"] = "Wrong Email format!";
                }
            }
            if (!empty($email)) {
                $getEmail = $conn->prepare("SELECT email FROM user WHERE email = ? AND id != ?");
                $getEmail->bind_param("si", $email, $id);
                $getEmail->execute();
                $getEmail->store_result();
                if ($getEmail->num_rows > 0) {
                    $error["error_status"] = true;
                    $error["email"] = "Email " . $email . " already registered!";
                }
            }
            $cPassword = cleaner($_POST["cPassword"], $conn);
            if (empty($password)) {
                $error["error_status"] = true;
                $error["password"] = "Password is required";
            }
            if (!empty($password)) {
                $patternPass = '/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/';
                if (!preg_match($patternPass, $password)) {
                    $error["error_status"] = true;
                    $error["password"] = "Password must contain at least one digit, one uppercase letter, and be 8-20 characters long";
                }
            }
            if ($password !== $cPassword) {
                $error["error_status"] = true;
                $error["cPassword"] = "Passwords do not match";
            }
            if (empty($cPassword)) {
                $error["error_status"] = true;
                $error["cPassword"] = "Password Confirmation is required";
            }
        }
        if ($error["error_status"] == true) {
            echo json_encode($error);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        if($type == 'edit'){
            try{
                // update user
                $stmt = $conn->prepare("UPDATE user SET npk=?, nama_lengkap=?, email=?, id_position=? WHERE id=?");
                $stmt->bind_param("issii", $npk, $name, $email, $idPosition, $id);
                if($stmt->execute()){
                    $response["status"] = "success";
                    $response["title"] = "Edit Success!";
                    $response["msg"] = "User data has been successfully changed!";
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
        else{
            try{
                // Insert to user
                $stmt = $conn->prepare("INSERT INTO user (npk, nama_lengkap, email, password, id_position) 
                                        VALUES(?, ?, ?, ?, ?)");
                $stmt->bind_param("isssi", $npk, $name, $email, $hashed_password, $idPosition);
                if($stmt->execute()){
                    $response["status"] = "success";
                    $response["title"] = "Registration Successful!";
                    $response["msg"] = "User has been successfully added!";
                }else{
                    $response["status"] = "error";
                    $response["title"] = "Registration Failed!";
                    $response["msg"] = $conn->error;
                };
                $stmt->close();
            } catch (Exception $e) {
                $conn->rollback();
                $response["status"] = "error";
                $response["title"] = "Registration Failed!";
                $response["msg"] = $e->getMessage();
            }
        }
    }

    // Close the connection
    $conn->close();
}
echo json_encode($response);
?>