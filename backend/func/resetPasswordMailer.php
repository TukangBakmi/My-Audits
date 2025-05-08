<?php

require_once '../../config/dbconn.php';
require_once '../func/cleaner.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../phpmailer/src/Exception.php';
require_once '../../phpmailer/src/PHPMailer.php';
require_once '../../phpmailer/src/SMTP.php';

$response = [];

// Get the JSON data sent in the request body
$json_data = file_get_contents('php://input');
    
// Decode JSON data into an associative array
$data = json_decode($json_data, true);

// Check if 'email' parameter exists in the JSON data
if (isset($_POST["email"]) || isset($data['email'])) {
    
    // Get the data
    // Change password from admin
    if (isset($_POST["email"])) {
        $email = cleaner($_POST["email"], $conn);
        $role = cleaner($_POST["role"], $conn);
    } 
    // forgot password
    else {
        $email = $data['email'];
        $role = $data['role'];
    }

    $txtRole = $role == 'admin' ? 'Admin' : 'User';
    
    // Check email is already registered or not (khusus untuk fitur forgot password pada halaman login)
    $getEmail = $conn->prepare("SELECT email FROM $role WHERE email = ?");
    $getEmail->bind_param("s", $email);
    $getEmail->execute();
    $getEmail->store_result();
    if ($getEmail->num_rows < 1) {
        $getEmail->close();
        $response['status'] = "error";
        $response["title"] = "Error!";
        $response["msg"] = "Email is not Registered";
    } else {

        $getEmail = $conn->prepare("SELECT * FROM $role WHERE email = ?");
        $getEmail->bind_param("s", $email);
        $getEmail->execute();
        
        $row = $getEmail->get_result()->fetch_assoc();
        $userId = $row['id'];
        $userNpk = $row['npk'];
        $name = $row['nama_lengkap'];
        $getEmail->close();

        // Generate a unique token
        $token = bin2hex(random_bytes(32));

        // Store the token and its expiration time in the database, associated with the user's email
        date_default_timezone_set('Asia/Bangkok'); // Set the time zone to UTC
        $expiryTime = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Token expires in 15 minutes
        $query = "INSERT INTO password_reset_tokens (id_user, npk_user, full_name, role, email, token, expiry_time) VALUES ('$userId', '$userNpk', '$name', '$role', '$email', '$token', '$expiryTime')";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Send a password reset email with a link containing the token
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // SMTP server
                $mail->SMTPAuth   = true;
                $mail->Username = "maybankfinance.mif@gmail.com";
                $mail->Password = "dkra doaa ewft zjmg";
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;

                // Recipients
                $mail->setFrom('maybankfinance.mif@gmail.com', 'Maybank Finance');
                $mail->addAddress($email); // Add recipient email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Maybank Finance Password Reset Request';
                $mail->Body    = '
                    <div style="width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; font-family: Arial, sans-serif; color: black; text-align: justify;">
                        <h2 style="text-align: center; color: #007bff;">Password Reset Request (' . $txtRole . ')</h2>
                        <hr style="border: 1px solid #007bff; margin-bottom: 20px;">
                
                        <p>Hello <strong>' . $txtRole . '</strong> ' . $name . ',</p>
                        <p>There was a request to change your password. Please click the following link to reset your password:</p>
                
                        <div style="text-align: center; margin: 20px 0;">
                            <a href="' . $rootURL . 'admin/resetPassword?id=' . $userId . '&token=' . $token . '&role=' . $role . '" style="background-color: #ffe23e; color: #000; text-decoration: none; padding: 10px 20px; border-radius: 5px; box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.6);">Reset Password</a>
                        </div>
                
                        <p>If you didn\'t make this request, please disregard this email.</p>
                        <p>Please note that this link will <strong>expire in 15 minutes</strong>. If your link has expired, you must submit a new password reset request.
                            Your password will not change unless you click the link above and create a new one.</p>
                        <p>If you have requested multiple reset emails, please make sure you <strong>click the link inside the most recent email</strong>.</p>
                        <strong>
                            <hr style="border: 1px solid #00000020; margin-top: 30px;">
                            <p>Sincerely,<br>
                            Maybank Finance Team</p>
                        </strong>
                    </div>';

                $mail->send();

                $response['status'] = "success";
                $response["title"] = "Success!";
                $response["msg"] = "Password reset email sent";
            } catch (Exception $e) {
                $response['status'] = "error";
                $response["title"] = "Error!";
                $response["msg"] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $response['status'] = "error";
            $response["title"] = "Error!";
            $response["msg"] = "Error occurred while processing your request";
        }

    }
} else {
    $response['status'] = "error";
    $response["title"] = "Error!";
    $response["msg"] = "Email parameter not found";
}

// Close the database connection
mysqli_close($conn);
echo json_encode($response);
?>