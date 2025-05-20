<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

session_start();
if (!isset($_SESSION["sta_user_id"])) {
    header("Location: ../login");
    exit();
}

// Path to the temporary QR code directory
$tempDir = __DIR__ . "/../temp_qr_codes";

// Load mail configuration
$mailConfig = require_once __DIR__ . "/../config/mail.php";

// Check if the form is submitted
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["send_qr"])) {
    try {
        // Include database connection
        require_once "../api/db.php";

        // Create temporary directory if it doesn't exist
        if (!is_dir($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception("Failed to create temporary directory");
            }
        }

        // Check if we need PHPMailer and QR Code libraries
        if (!file_exists(__DIR__ . "/../vendor/autoload.php")) {
            throw new Exception("Required libraries not found. Please run:<br>
        <code>composer require phpmailer/phpmailer endroid/qr-code</code>");
        }

        // Include the libraries
        require_once __DIR__ . "/../vendor/autoload.php";

        // Get SMTP settings from form or use defaults from config
        $smtpServer = !empty($_POST["smtp_server"])
            ? $_POST["smtp_server"]
            : $mailConfig["smtp_server"];
        $smtpPort = !empty($_POST["smtp_port"])
            ? $_POST["smtp_port"]
            : $mailConfig["smtp_port"];
        $smtpUsername = !empty($_POST["smtp_username"])
            ? $_POST["smtp_username"]
            : $mailConfig["smtp_username"];
        $smtpPassword = !empty($_POST["smtp_password"])
            ? $_POST["smtp_password"]
            : $mailConfig["smtp_password"];
        $senderName = !empty($_POST["sender_name"])
            ? $_POST["sender_name"]
            : $mailConfig["sender_name"];

        // Query to get all active students with email addresses
        $query = $pdo->prepare(
            "SELECT urn, name, email FROM Students WHERE deleted = 0 AND email IS NOT NULL AND email != ''"
        );
        $query->execute();
        $students = $query->fetchAll(PDO::FETCH_ASSOC);

        if (count($students) === 0) {
            throw new Exception("No students with valid email addresses found");
        }

        $successCount = 0;
        $failCount = 0;
        $failedStudents = [];

        foreach ($students as $student) {
            $urn = $student["urn"];
            $name = $student["name"];
            $email = $student["email"];

            // Skip invalid emails
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failCount++;
                $failedStudents[] = "$name ($email) - Invalid email format";
                continue;
            }

            // Generate QR code
            $qrCodeFileName = "$tempDir/qrcode_$urn.png";

            try {
                // Generate QR code using Endroid QR Code library
                $qrCode = new QrCode($urn);
                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $result->saveToFile($qrCodeFileName);

                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                // Server settings
                $mail->isSMTP();
                $mail->Host = $smtpServer;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUsername;
                $mail->Password = $smtpPassword;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $smtpPort;

                // Recipients
                $mail->setFrom($smtpUsername, $senderName);
                $mail->addAddress($email, $name);

                // Attachments
                $mail->addAttachment($qrCodeFileName, "QRCode_$urn.png");

                // Content
                $mail->isHTML(true);
                $mail->Subject = $mailConfig["qr_code_subject"];
                $htmlBody = str_replace(
                    "{name}",
                    $name,
                    $mailConfig["qr_code_body_html"]
                );
                $textBody = str_replace(
                    "{name}",
                    $name,
                    $mailConfig["qr_code_body_text"]
                );
                $mail->Body = $htmlBody;
                $mail->AltBody = $textBody;

                // Send the email
                $mail->send();
                $successCount++;
            } catch (Exception $e) {
                $failCount++;
                $failedStudents[] = "$name ($email) - " . $e->getMessage();
            }

            // Remove the QR code file
            if (file_exists($qrCodeFileName)) {
                unlink($qrCodeFileName);
            }
        }

        // Remove the temporary directory
        if (is_dir($tempDir)) {
            rmdir($tempDir);
        }

        // Set messages based on results
        if ($successCount > 0) {
            $successMessage = "Successfully sent QR codes to $successCount students.";
        }

        if ($failCount > 0) {
            $errorMessage =
                "Failed to send QR codes to $failCount students:<br>" .
                implode("<br>", $failedStudents);
        }
    } catch (Exception $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Send QR Codes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="container p-3">
  <h1>Send QR Codes to Students</h1>

  <div class="d-flex flex-sm-row flex-column gap-2 mb-3">
    <a href="../home" class="btn btn-danger">Back to Home</a>
  </div>

  <?php if ($successMessage): ?>
    <div class="alert alert-success"><?php echo $successMessage; ?></div>
  <?php endif; ?>

  <?php if ($errorMessage): ?>
    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
  <?php endif; ?>

  <div class="row">
    <div class="col-lg-6 col-md-8 col-sm-12 mx-auto">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title mb-0">Email QR Codes</h3>
        </div>
        <div class="card-body">
          <p>
            This will generate QR codes for all students and send them via email.
            Make sure all students have valid email addresses in the system.
          </p>

          <form method="post" action="">
            <div class="mb-3">
              <label for="smtp_server" class="form-label">SMTP Server</label>
              <input type="text" class="form-control" id="smtp_server" name="smtp_server" value="<?php echo htmlspecialchars(
                  $mailConfig["smtp_server"]
              ); ?>" required>
            </div>

            <div class="mb-3">
              <label for="smtp_port" class="form-label">SMTP Port</label>
              <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars(
                  $mailConfig["smtp_port"]
              ); ?>" required>
            </div>

            <div class="mb-3">
              <label for="smtp_username" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars(
                  $mailConfig["smtp_username"]
              ); ?>" required>
              <div class="form-text">This will be used as the sender email address</div>
            </div>

            <div class="mb-3">
              <label for="smtp_password" class="form-label">Email Password or App Password</label>
              <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars(
                  $mailConfig["smtp_password"]
              ); ?>" required>
              <div class="form-text">For Gmail, you need to use an App Password</div>
            </div>

            <div class="mb-3">
              <label for="sender_name" class="form-label">Sender Name</label>
              <input type="text" class="form-control" id="sender_name" name="sender_name" value="<?php echo htmlspecialchars(
                  $mailConfig["sender_name"]
              ); ?>" required>
            </div>

            <div class="d-grid">
              <button type="submit" name="send_qr" class="btn btn-primary">Send QR Codes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
