<?php

require_once '../../config/dbconn.php';
require_once '../../vendor/autoload.php';
require_once '../func/cleaner.php';

$uploadDir = '../../assets/uploads/';
$response = [];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['download']) && $_GET['download'] === 'true') {
        
        // File Name
        $fileName = "Login User Logs.xlsx";
        // Header in Excel
        $column = array("Id", "NPK", "Nama Lengkap", "Tanggal Login", "Tanggal Logout", "Sudah Logout?");
        // Excel Data
        $sql = "SELECT id, npk_user, full_name, date_login, date_logout, logged_out FROM visitor";

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
            $row_data["logged_out"] = $row_data["logged_out"] ? "Sudah" : "Belum";
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
    
    else{
        $result = $conn->query("SELECT * FROM visitor");
        if (!$result) {
            $response["msg"] = $conn->error;
            $response["status"] = "error";
        } else {
            while ($row = $result->fetch_assoc()) {
                $response["data"][] = $row;
            }
            $response["msg"] = "Success";
            $response["status"] = "success";
        }
    }
    
}

$conn->close();
echo json_encode($response);
?>