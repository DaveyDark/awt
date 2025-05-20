<?php
/**
 * Student Attendance System - Mail Configuration
 *
 * This file contains the SMTP settings used throughout the application.
 * Edit this file to match your email server settings.
 */

// SMTP configuration settings
return [
    "smtp_server" => "smtp.gmail.com", // SMTP server address
    "smtp_port" => 587, // SMTP port (typically 587 for TLS, 465 for SSL)
    "smtp_secure" => "tls", // Security protocol: 'tls', 'ssl', or ''
    "smtp_username" => "example@gmail.com", // Your email address
    "smtp_password" => "your-app-password", // Your email password or app password
    "sender_name" => "Student Attendance System", // Name displayed as sender
    "from_email" => "noreply@gmail.com", // From email address (if different from username)
    "reply_to" => "support@example.com", // Reply-to email address

    // Email templates
    "qr_code_subject" => "Your Attendance QR Code",
    "qr_code_body_html" => '<p>Hello {name},</p>
<p>Attached is your QR code for the attendance system. Please keep it safe and present it for attendance scanning.</p>
<p>Regards,<br>Student Attendance System</p>',
    "qr_code_body_text" =>
        "Hello {name},\n\nAttached is your QR code for the attendance system. Please keep it safe and present it for attendance scanning.\n\nRegards,\nStudent Attendance System",

    // Advanced settings
    "smtp_timeout" => 30, // Connection timeout in seconds
    "smtp_auth" => true, // Enable SMTP authentication
    "smtp_debug" => 0, // Debug level (0-4): 0=no output, 4=verbose
    "charset" => "utf-8", // Email character set
];
