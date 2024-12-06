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
?>
