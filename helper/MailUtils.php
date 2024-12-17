<?php
// Include PHPMailer classes (make sure PHPMailer is included in your project)
require 'vendor/phpmailer/phpmailer/PHPMailerAutoload.php';

// Create a new PHPMailer instance
function sendEmail($to, $subject, $body){
$mail = new PHPMailer;

// Set SMTP options
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com'; // Set the SMTP server to Gmail
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'itguyrw@gmail.com'; // Your Gmail address
$mail->Password = 'epyrtxnbfgglddtu'; // Your Gmail password or App Password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
$mail->Port = 587; // Set the TCP port to connect to (587 for TLS)

// Set the email sender and recipient
$mail->setFrom('itguyrw@gmail.com', 'Residence MIS'); // Sender's email and name
$mail->addAddress('damn@yopmail.com', 'Recipient asua'); // Recipient's email and name

// Set email subject and body
$mail->Subject = 'Test Email from PHP';
$mail->Body    = 'This is a test email sent from PHP using Gmail SMTP.';

// Send the email
if ($mail->send()) {
    echo 'Email sent successfully!';
} else {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
}
?>
