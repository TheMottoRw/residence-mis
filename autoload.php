<?php
// Include Composer's autoloader
require 'vendor/autoload.php';

// Import necessary classes
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\LabelAlignment;

// Generate the QR code content (this can be a URL or any string)
$verificationUrl = 'https://example.com/certificate/verify?cert_id=12345';

// Create a new QR code instance
$qrCode = new QrCode($verificationUrl);

// Optionally, set some properties (size, error correction level, encoding, etc.)
$qrCode->setSize(200); // Set the size of the QR code (default is 300px)
$qrCode->setMargin(10); // Set the margin (default is 10px)
$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH); // Set error correction level (LOW, MEDIUM, QUARTILE, HIGH)
$qrCode->setEncoding(Encoding::UTF_8); // Set encoding (default is UTF-8)

// Create a writer instance to generate the image
$writer = new PngWriter();

// Save the QR code to a file (optional)
$qrCodePath = 'certificate_qr.png';
$writer->writeFile($qrCode, $qrCodePath); // Writes the QR code to a PNG file

// Alternatively, you can directly output the QR code as an image (optional)
header('Content-Type: image/png');
echo $writer->writeString($qrCode); // Directly output the PNG image

// Display the path to the saved image (optional)
echo "QR Code saved to: " . $qrCodePath;
// Use the PHPMailer class
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer files (use Composer autoloader or manual inclusion)
require 'vendor/autoload.php'; // Composer autoloader, or use the path to PHPMailer's files

$mail = new PHPMailer(true);

try {
    // Set mailer to use SMTP
    $mail->isSMTP();

    // Set the SMTP server to Gmail's server
    $mail->Host       = 'smtp.gmail.com';            // Gmail SMTP server
    $mail->SMTPAuth   = true;                        // Enable SMTP authentication
    $mail->Username   = 'your-email@gmail.com';      // Your Gmail address
    $mail->Password   = 'your-email-password';       // Your Gmail password or App Password (if 2FA enabled)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS encryption
    $mail->Port       = 587;                         // Gmail SMTP port for STARTTLS

    // Recipients
    $mail->setFrom('your-email@gmail.com', 'CRMS');  // Your Gmail address (sender)
    $mail->addAddress($email, 'Recipient Name');      // Add recipient email

    // Email Content
    $mail->isHTML(true);                             // Set email format to HTML
    $mail->Subject = 'Password Reset Request';       // Email subject
    $mail->Body    = 'Click the link below to reset your password:<br><a href="' . $resetLink . '">Reset Password</a>'; // Email body

    // Send the email
    $mail->send();
    echo 'Password reset email has been sent to your email address.';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>
