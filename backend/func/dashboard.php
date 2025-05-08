<?php


// ---------------------------------------- GRAPH ---------------------------------------- //

// Check if a parameter named 'graphType' is provided in the URL
if(isset($_GET['graph'])) {
    
    require_once '../../config/dbconn.php';

    $graphType = $_GET['graph'];

    if($graphType === 'date') {
        $groupBy = $_GET['group'];
        $datetime = $_GET['time'];

        $currentMonth = date("m"); // Get current month (2 digits, leading zero)
        $currentYear = date("Y"); // Get current year (4 digits)

        if ($groupBy == 'byDate'){
            if ($datetime == 'allTime') {
                $query = "SELECT date_downloaded, COUNT(*) AS count FROM log_download 
                        GROUP BY DATE(date_downloaded) ORDER BY date_downloaded";
            }
            else if ($datetime == 'month') {
                $query = "SELECT date_downloaded, COUNT(*) AS count FROM log_download 
                        WHERE MONTH(date_downloaded) = $currentMonth AND YEAR(date_downloaded) = $currentYear 
                        GROUP BY DATE(date_downloaded) ORDER BY date_downloaded";
            }
            else if ($datetime == 'year') {
                $query = "SELECT date_downloaded, COUNT(*) AS count FROM log_download 
                        WHERE YEAR(date_downloaded) = $currentYear 
                        GROUP BY DATE(date_downloaded) ORDER BY date_downloaded";
            }
            else {
                list($startDate, $endDate) = explode(' | ', $datetime);
                $query = "SELECT date_downloaded, COUNT(*) AS count FROM log_download 
                        WHERE DATE(date_downloaded) BETWEEN '$startDate' AND '$endDate'
                        GROUP BY DATE(date_downloaded) ORDER BY date_downloaded";
            };
        }

        else if ($groupBy == 'byMonth'){
            if ($datetime == 'allTime') {
                $query = "SELECT date_downloaded, DATE_FORMAT(date_downloaded, '%Y-%m') AS month_downloaded, COUNT(*) AS count FROM log_download 
                        GROUP BY month_downloaded ORDER BY month_downloaded";
            }
            else if ($datetime == 'month') {
                echo json_encode([]);
                exit();
            }
            else if ($datetime == 'year') {
                $query = "SELECT date_downloaded, DATE_FORMAT(date_downloaded, '%Y-%m') AS month_downloaded, COUNT(*) AS count FROM log_download 
                        WHERE YEAR(date_downloaded) = $currentYear 
                        GROUP BY month_downloaded ORDER BY month_downloaded";
            }
            else {
                list($startDate, $endDate) = explode(' | ', $datetime);
                $query = "SELECT date_downloaded, DATE_FORMAT(date_downloaded, '%Y-%m') AS month_downloaded, COUNT(*) AS count FROM log_download 
                        WHERE DATE(date_downloaded) BETWEEN '$startDate' AND '$endDate'
                        GROUP BY month_downloaded ORDER BY month_downloaded";
            };
        }

        $result = $conn->query($query);

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    if($graphType === 'file') {
        // Fetch data from the database (replace with your query)
        $query = "SELECT *, COUNT(*) AS count FROM log_download GROUP BY file_name ORDER BY count DESC LIMIT 10";
        $result = $conn->query($query);

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    if($graphType === 'user') {
        // Fetch data from the database (replace with your query)
        $query = "SELECT CONCAT(nama_lengkap, ' (', npk_user, ')') AS username, COUNT(*) AS count 
                FROM log_download a LEFT JOIN user b ON a.id_user = b.id WHERE a.position != 'Admin' GROUP BY id_user ORDER BY count DESC";
        $result = $conn->query($query);

        $data = array();

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}

else {
    require_once '../config/dbconn.php';

    // Get current date with format DD MMMMM YYYY
    $currentDate = date('j F Y');
    
    // ---------------------------------------- COUNTING TRAFFIC ---------------------------------------- //
    
    // Select data traffic
    $query = "SELECT * FROM visitor WHERE logged_out = 'false'";
    $result = $conn->query($query);
    
    // Count the number of traffic
    $traffic = $result->num_rows;
    
    
    
    // ---------------------------------------- COUNTING NEW FILES ---------------------------------------- //
    
    // Select data from the last 3 days
    $query = "SELECT * FROM file WHERE date_uploaded >= DATE_SUB(NOW(), INTERVAL 3 DAY)";
    $result = $conn->query($query);
    
    // Count the number of new files
    $newFile = $result->num_rows;
    
    
    
    // ---------------------------------------- COUNTING TOTAL DOWNLOADS ---------------------------------------- //
    
    // Select data from today
    $query = "SELECT * FROM log_download WHERE DATE(date_downloaded) = CURDATE()";
    $result = $conn->query($query);
    // Count the number of download today
    $todayDownload = $result->num_rows;

    // Select data from today
    $query = "SELECT * FROM log_download";
    $result = $conn->query($query);
    // Count the number of total download
    $countDownload = $result->num_rows;
    
    
    
    // ---------------------------------------- COUNTING TOTAL VISITORS ---------------------------------------- //
    
    // Select data from visitor
    $query = "SELECT * FROM visitor";
    $result = $conn->query($query);
    
    // Count the number of visitor
    $countVisitor = $result->num_rows;
    
    
    
    // ---------------------------------------- LATEST VISITOR ---------------------------------------- //
    
    $onlineVisitors = [];
    $pastVisitors = [];
    
    $online = $conn->query("SELECT * FROM visitor WHERE logged_out = 'false'");
    
    $getLatestQuery = "SELECT MAX(date_logout) FROM visitor b WHERE a.npk_user = b.npk_user AND logged_out = 'true' AND DATE(date_logout) = CURDATE()";
    $getDistinctQuery = "SELECT 1 FROM visitor b WHERE a.npk_user = b.npk_user AND logged_out = 'false'";
    
    $past = $conn->query("SELECT * FROM visitor a WHERE date_logout = ($getLatestQuery) AND NOT EXISTS ($getDistinctQuery)
                        ORDER BY date_logout DESC LIMIT 12");
    
    while ($row = $online->fetch_assoc()) {
        $onlineVisitors[] = $row;
    }
    
    while ($row = $past->fetch_assoc()) {
        // Select the time
        $explode = explode(" ", $row["date_logout"]);
        $row["date_logout"] = $explode[1];
        $pastVisitors[] = $row;
    }
}

$conn->close();

?>