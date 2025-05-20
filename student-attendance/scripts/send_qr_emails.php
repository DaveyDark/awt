<?php
/**
 * Send QR Codes Email Script
 *
 * This script reads student data from a CSV file, generates QR codes,
 * and sends them to students via email.
 */

// Load mail configuration
$mailConfig = require_once __DIR__ . '/../config/mail.php';

// SMTP Configuration
define("SMTP_HOST", $mailConfig['smtp_server']);
define("SMTP_USERNAME", $mailConfig['smtp_username']);
define("SMTP_PASSWORD", $mailConfig['smtp_password']);
define("SMTP_PORT", $mailConfig['smtp_port']);
define("SMTP_FROM_EMAIL", $mailConfig['from_email'] ?: $mailConfig['smtp_username']);
define("SMTP_FROM_NAME", $mailConfig['sender_name']);

// Include the PHPMailer library
// Note: You need to install PHPMailer via Composer first
// composer require phpmailer/phpmailer
require_once __DIR__ . "/../vendor/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to generate a QR code for the given URN
function generateQRCode($urn, $fileName)
{
    // We're using the PHP QR Code library
    // You need to install it via Composer:
    // composer require endroid/qr-code

    $qrCode = new \Endroid\QrCode\QrCode($urn);
    $qrCode->setSize(300);
    $qrCode->setMargin(10);
    $qrCode->writeFile($fileName);

    return true;
}

// Function to validate an email address
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to send an email with the QR code attached
function sendEmail(
    $recipientEmail,
    $recipientName,
    $subject,
    $body,
    $qrCodeFileName
) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);

        // Attachments
        $mail->addAttachment($qrCodeFileName);

        // Content
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send the email
        $mail->send();
        echo "Email sent successfully to $recipientEmail<br>";
        return true;
    } catch (Exception $e) {
        echo "Failed to send email to $recipientEmail: {$mail->ErrorInfo}<br>";
        return false;
    }
}

// Main script execution
try {
    // Path to the CSV file
    $csvFile = __DIR__ . "/../students.csv";

    // Check if the file exists
    if (!file_exists($csvFile)) {
        throw new Exception("CSV file not found: $csvFile");
    }

    // Create a temporary directory to store QR codes
    $tempDir = __DIR__ . "/../temp_qr_codes";
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // Open the CSV file
    $handle = fopen($csvFile, "r");
    if ($handle === false) {
        throw new Exception("Could not open CSV file");
    }

    // Read the header row
    $header = fgetcsv($handle);
    if ($header === false) {
        throw new Exception("Could not read CSV header");
    }

    // Find the column indexes
    $urnIndex = array_search("urn", $header);
    $nameIndex = array_search("name", $header);
    $emailIndex = array_search("email", $header);

    if ($urnIndex === false || $nameIndex === false || $emailIndex === false) {
        throw new Exception(
            "CSV file must contain 'urn', 'name', and 'email' columns"
        );
    }

    // Read each line of the CSV file
    $successCount = 0;
    $failCount = 0;

    while (($data = fgetcsv($handle)) !== false) {
        $urn = trim($data[$urnIndex]);
        $name = trim($data[$nameIndex]);
        $email = trim($data[$emailIndex]);

        // Validate email
        if (!validateEmail($email)) {
            echo "Invalid email address: $email. Skipping...<br>";
            $failCount++;
            continue;
        }

        // Generate QR code
        $qrCodeFileName = "$tempDir/qrcode_$urn.png";
        if (!generateQRCode($urn, $qrCodeFileName)) {
            echo "Failed to generate QR code for $name. Skipping...<br>";
            $failCount++;
            continue;
        }

        // Prepare email content
        $subject = $mailConfig['qr_code_subject'];
        $body = str_replace('{name}', $name, $mailConfig['qr_code_body_text']);

        // Send email
        if (sendEmail($email, $name, $subject, $body, $qrCodeFileName)) {
            $successCount++;
        } else {
            $failCount++;
        }

        // Remove the QR code file
        unlink($qrCodeFileName);
    }

    // Close the CSV file
    fclose($handle);

    // Remove the temporary directory
    rmdir($tempDir);

    // Display summary
    echo "<h2>Summary</h2>";
    echo "Successfully sent: $successCount<br>";
    echo "Failed: $failCount<br>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
