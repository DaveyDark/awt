# Student Attendance System

A comprehensive web-based system for managing student attendance using QR codes. This application allows administrators to track student attendance efficiently by scanning QR codes assigned to each student.

## Features

- **Admin Authentication**: Secure login system for administrators
- **Student Management**: Add, edit, and delete student records
- **QR Code Generation**: Generate unique QR codes for each student
- **QR Code Distribution**: Send QR codes to students via email
- **Attendance Tracking**: Mark attendance by scanning student QR codes
- **Attendance Reports**: View attendance records by date
- **Centralized Configuration**: Easy database and email settings management

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB
- Web server (Apache, Nginx, etc.)
- Composer (optional, for enhanced functionality)

### Setup Instructions

1. **Clone or download the repository**
   
   Place the application in your web server's document root.

2. **Configure the database**
   
   Edit the database connection settings in `config/database.php`:
   
   ```php
   return [
       "host" => "localhost", // Database host
       "username" => "root",  // Database username
       "password" => "",      // Database password
       "database" => "student_attendance", // Database name
       "charset" => "utf8mb4", // Character set
       "port" => 3306,        // Database port
   ];
   ```

3. **Set up email functionality** (optional)
   
   Edit the email settings in `config/mail.php`:
   
   ```php
   return [
       'smtp_server' => 'smtp.gmail.com',  // SMTP server address
       'smtp_port' => 587,                 // SMTP port
       'smtp_username' => 'your-email@gmail.com', // Your email address
       'smtp_password' => 'your-password', // Your password or app password
       'sender_name' => 'Student Attendance System'
   ];
   ```

4. **Create database and tables**
   
   Run the database setup script:
   
   ```
   php scripts/create_db.php
   ```

5. **Set up a default admin user**
   
   Run the database seeding script:
   
   ```
   php scripts/seed_db.php
   ```

6. **Install PHP dependencies** (optional, but recommended)
   
   ```
   composer require phpmailer/phpmailer endroid/qr-code
   ```

7. **Create temporary directories**
   
   ```
   mkdir -p temp_qr_codes
   chmod 777 temp_qr_codes
   ```

## Importing Student Data

You can import student data from a CSV file:

1. **Prepare your CSV file with the following columns**:
   - `urn` (Student ID)
   - `name` (Student Name)
   - `phone` (optional)
   - `email` (optional)

2. **Place the CSV file**
   
   Save your file as `students.csv` in the project root directory.

3. **Run the import script**
   
   ```
   php scripts/import_csv.php
   ```

## Usage

1. **Login**
   
   Access the login page and use the following default credentials:
   - Username: admin
   - Password: admin

2. **Manage Students**
   
   Add, edit, or delete student records from the student management page.

3. **Generate QR Codes**
   
   Each student can have their QR code generated from the student management page.

4. **Send QR Codes via Email**
   
   Use the "Send QR Codes" feature to email QR codes to all students with email addresses.

5. **Mark Attendance**
   
   Use the QR scanner on the home page to scan student QR codes and mark attendance.

6. **View Attendance Reports**
   
   Navigate to the attendance page to view attendance records by date.

## System Architecture

### Database Structure

- **Admins**: Stores administrator credentials
- **Students**: Stores student information (URN, name, branch, phone, email)
- **Attendance**: Records daily attendance with date and student URN

### File Structure

```
student-attendance/
├── api/                  # API endpoints
├── attendance/           # Attendance viewing page
├── config/               # Configuration files
├── home/                 # Home page with QR scanner
├── js/                   # JavaScript files
├── login/                # Login page
├── scripts/              # Setup and utility scripts
├── send_qr/              # QR code email sending page
├── students/             # Student management page
├── temp_qr_codes/        # Temporary QR code storage
└── vendor/               # Composer dependencies
```

### Configuration Files

- **config/database.php**: Database connection settings
- **config/mail.php**: Email and SMTP settings

## Troubleshooting

### Database Connection Issues
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database and table existence

### Email Sending Problems
- Verify SMTP credentials in `config/mail.php`
- For Gmail, use an App Password if you have 2FA enabled
- Check firewall settings for outgoing SMTP connections

### QR Code Generation Issues
- Ensure the Composer dependencies are installed
- Check write permissions for the temp_qr_codes directory

## Security Considerations

- Passwords are stored as hashed values
- SQL injection protection with prepared statements
- Session-based authentication
- Input validation and sanitization

## License

This project is licensed under the MIT License.