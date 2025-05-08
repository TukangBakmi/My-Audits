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
        $fileName = "Download Logs.xlsx";
        // Header in Excel
        $column = array("Id", "Id File", "Nama File", "Raw File Size", "Formatted File Size", "Di-Download Oleh", "NPK", "Jabatan", "Tanggal di-Download");
        // Excel Data
        $sql = "SELECT id, id_file, file_name, file_size, FORMAT(file_size, 2) as formatted_size, full_name, npk_user, position, date_downloaded FROM log_download";

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
            $row_data["formatted_size"] = formatSizeUnits($row_data["file_size"]);
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
        $result = $conn->query("SELECT *, FORMAT(file_size, 2) as formatted_size FROM log_download");
        if (!$result) {
            $response["msg"] = $conn->error;
            $response["status"] = "error";
        } else {
            while ($row = $result->fetch_assoc()) {
                $row["formatted_size"] = formatSizeUnits($row["file_size"]);
                $response["data"][] = $row;
            }
            $response["msg"] = "Success";
            $response["status"] = "success";
        }
    }
    
}

$conn->close();
echo json_encode($response);

function formatSizeUnits($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>