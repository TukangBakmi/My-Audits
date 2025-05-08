<?php
require_once '../../config/dbconn.php';
function cleaner($data, $conn)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = strip_tags(mysqli_real_escape_string($conn, trim($data)));
    return $data;
}
