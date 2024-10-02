<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $selection = $conn->real_escape_string(trim($_POST['selection']));

    $stmt = $conn->prepare("INSERT INTO users (name, email, selection) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $selection);
        
        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'mohsinasif12788@gmail.com';
                $mail->Password   = 'xcvi nhep jept yvuf'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Recipients
                $mail->setFrom('mohsinasif12788@gmail.com', 'leads');
                $mail->addAddress('farooqasif433@gmail.com', 'hamari website');

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'The leads';
                $mail->Body    = 'This is the HTML message body <b>in bold!</b>';

                $mail->send();
                $message = "<div class='success'>Message Has Been Sent!</div>"; 
            } catch (Exception $e) {
                $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $message = "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>User Data Form</h2>
<form method="post" action="">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="selection">Select Option:</label>
    <select id="selection" name="selection" required>
        <option value="">Select...</option>
        <option value="Option 1">apple</option>
        <option value="Option 2">banana</option>
        <option value="Option 3">Orange</option>
    </select>

    <input type="submit" value="Submit">
</form>

<?php
if ($message) {
    echo $message; 
}
?>

</body>
</html>
