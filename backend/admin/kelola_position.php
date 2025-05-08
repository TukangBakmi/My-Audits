<?php

require_once '../../config/dbconn.php';
require_once '../../vendor/autoload.php';
require_once '../func/cleaner.php';

$response = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['download']) && $_GET['download'] === 'true') {

        // File Name
        $fileName = "Position.xlsx";
        // Header in Excel
        $column = array("Id", "Nama Jabatan");
        // Excel Data
        $sql = "SELECT * FROM position";

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

        if ($type == 'position_record') {
            $id = $_GET['id'];
            $result = $conn->query("SELECT * FROM position WHERE id = $id");
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

    $type = $_POST["type"];

    if($type == 'add'){
        // Getting all data
        $position = cleaner($_POST["position"], $conn);
        
        // Validate
        $getPosition = $conn->prepare("SELECT position FROM position WHERE position = ?");
        $getPosition->bind_param("s", $position);
        $getPosition->execute();
        $getPosition->store_result();
        if ($getPosition->num_rows > 0) {
            $response["status"] = "error";
            $response["title"] = "Error";
            $response["msg"] = "Position " . $position . " already exists";
            echo json_encode($response);
            exit();
        }

        try{
            // Insert to user
            $stmt = $conn->prepare("INSERT INTO position (position) VALUES(?)");
            $stmt->bind_param("s", $position);
            if($stmt->execute()){
                $response["status"] = "success";
                $response["title"] = "Success!";
                $response["msg"] = "New position has been successfully added!";
            }else{
                $response["status"] = "error";
                $response["title"] = "Error!";
                $response["msg"] = $conn->error;
            };
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            $response["status"] = "error";
            $response["title"] = "Error!";
            $response["msg"] = $e->getMessage();
        }
    }


    if($type == 'edit'){
        // Getting all data
        $id = cleaner($_POST["id"], $conn);
        $currentId = cleaner($_POST["currentId"], $conn);
        $position = cleaner($_POST["positionName"], $conn);

        // Validate
        $error = array("error_status" => false);

        if (empty($id)) {
            $error["error_status"] = true;
            $error["id"] = "ID is required!";
        }
        if (!empty($id)) {
            $getId = $conn->prepare("SELECT id FROM position WHERE id = ? AND id != ?");
            $getId->bind_param("ii", $id, $currentId);
            $getId->execute();
            $getId->store_result();
            if ($getId->num_rows > 0) {
                $error["error_status"] = true;
                $error["id"] = "Id " . $id . " already exists!";
            }
        }
        if (empty($position)) {
            $error["error_status"] = true;
            $error["positionName"] = "Position is required!";
        }
        if ($error["error_status"] == true) {
            echo json_encode($error);
            exit();
        }

        try{
            // update user
            $stmt = $conn->prepare("UPDATE position SET id=?, position=? WHERE id=?");
            $stmt->bind_param("isi", $id, $position, $currentId);
            if($stmt->execute()){
                $response["status"] = "success";
                $response["title"] = "Edit Success!";
                $response["msg"] = "Position has been successfully changed!";
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


    if($type == 'delete'){
        try{
            // delete position
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM position WHERE id = ?");
            $stmt->bind_param("i", $id);
            if($stmt->execute()){
                $response["status"] = "success";
                $response["title"] = "Delete Success!";
                $response["msg"] = "Position has been successfully deleted!";
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

    // Close the connection
    $conn->close();
}
echo json_encode($response);
?>