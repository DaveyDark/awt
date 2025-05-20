<?php
/**
 * Student Attendance System - Installation Script
 * 
 * This script helps set up the Student Attendance System by:
 * 1. Creating the database and tables
 * 2. Setting up an initial admin user
 * 3. Installing required dependencies (if Composer is available)
 */

$steps = [
    'database' => [
        'status' => 'pending',
        'message' => 'Creating database and tables...'
    ],
    'admin' => [
        'status' => 'pending',
        'message' => 'Setting up admin user...'
    ],
    'composer' => [
        'status' => 'pending',
        'message' => 'Installing dependencies...'
    ]
];

$errors = [];
$installed = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    // Database setup
    try {
        // Get database connection details
        $servername = $_POST['db_host'];
        $username = $_POST['db_username'];
        $password = $_POST['db_password'];
        $dbname = $_POST['db_name'];

        // Create PDO connection
        $pdo = new PDO("mysql:host=$servername", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $pdo->exec("USE `$dbname`");

        // Create tables
        $createQueries = [
            "
            CREATE TABLE IF NOT EXISTS Admins (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                deleted BOOLEAN NOT NULL DEFAULT FALSE,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ",
            "
            CREATE TABLE IF NOT EXISTS Students (
                urn VARCHAR(7) PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                branch VARCHAR(3) NOT NULL,
                phone VARCHAR(15) NULL,
                email VARCHAR(255) NULL,
                deleted BOOLEAN NOT NULL DEFAULT FALSE,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ",
            "
            CREATE TABLE IF NOT EXISTS Attendance (
                id INT PRIMARY KEY AUTO_INCREMENT,
                urn VARCHAR(7) NOT NULL,
                date DATE NOT NULL,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (urn) REFERENCES Students(urn)
            );
            "
        ];

        foreach ($createQueries as $query) {
            $pdo->exec($query);
        }

        // Update database settings in db.php
        $dbConfigPath = __DIR__ . '/api/db.php';
        if (file_exists($dbConfigPath)) {
            $dbConfig = file_get_contents($dbConfigPath);
            $dbConfig = preg_replace('/\$servername = ".*?";/', '$servername = "' . $servername . '";', $dbConfig);
            $dbConfig = preg_replace('/\$dbusername = ".*?";/', '$dbusername = "' . $username . '";', $dbConfig);
            $dbConfig = preg_replace('/\$dbpassword = ".*?";/', '$dbpassword = "' . $password . '";', $dbConfig);
            $dbConfig = preg_replace('/\$dbname = ".*?";/', '$dbname = "' . $dbname . '";', $dbConfig);
            file_put_contents($dbConfigPath, $dbConfig);
        }

        $steps['database']['status'] = 'success';
    } catch (PDOException $e) {
        $steps['database']['status'] = 'error';
        $errors[] = "Database setup failed: " . $e->getMessage();
    }

    // Create admin user
    if ($steps['database']['status'] === 'success') {
        try {
            $adminUsername = $_POST['admin_username'];
            $adminPassword = $_POST['admin_password'];
            
            // Hash the password
            $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

            // Insert admin user
            $stmt = $pdo->prepare("INSERT INTO Admins (name, password) VALUES (:name, :password)");
            $stmt->execute([
                'name' => $adminUsername,
                'password' => $hashedPassword
            ]);

            $steps['admin']['status'] = 'success';
        } catch (PDOException $e) {
            $steps['admin']['status'] = 'error';
            $errors[] = "Admin user setup failed: " . $e->getMessage();
        }
    }

    // Install dependencies with Composer
    if (isset($_POST['install_composer']) && $_POST['install_composer'] === 'yes') {
        try {
            // Check if Composer is installed
            $composerPath = trim(shell_exec('which composer') ?: 'composer');
            $output = [];
            $returnVar = 0;
            
            // Run Composer install
            exec("$composerPath install --no-dev", $output, $returnVar);
            
            if ($returnVar === 0) {
                $steps['composer']['status'] = 'success';
            } else {
                throw new Exception("Composer install failed. Return code: $returnVar");
            }
        } catch (Exception $e) {
            $steps['composer']['status'] = 'error';
            $errors[] = "Dependency installation failed: " . $e->getMessage();
        }
    } else {
        $steps['composer']['status'] = 'skipped';
    }

    // Check if installation was successful
    $installed = $steps['database']['status'] === 'success' && 
                $steps['admin']['status'] === 'success' &&
                ($steps['composer']['status'] === 'success' || $steps['composer']['status'] === 'skipped');

    // Create a file to indicate successful installation
    if ($installed) {
        file_put_contents(__DIR__ . '/installed.txt', 'Installed on ' . date('Y-m-d H:i:s'));
    }
}

// Check if already installed
$alreadyInstalled = file_exists(__DIR__ . '/installed.txt');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Student Attendance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        .step-indicator {
            width: 20px;
            height: 20px;
            display: inline-block;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
        }
        .pending { background-color: #6c757d; }
        .success { background-color: #28a745; }
        .error { background-color: #dc3545; }
        .skipped { background-color: #ffc107; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h1 class="h3 mb-0">Student Attendance System Installation</h1>
                    </div>
                    <div class="card-body">
                        <?php if ($alreadyInstalled && !$installed): ?>
                            <div class="alert alert-info">
                                <h4>System Already Installed</h4>
                                <p>The Student Attendance System appears to be already installed.</p>
                                <p>If you want to reinstall, please delete the <code>installed.txt</code> file in the root directory.</p>
                                <a href="index.php" class="btn btn-primary">Go to Login</a>
                            </div>
                        <?php elseif ($installed): ?>
                            <div class="alert alert-success">
                                <h4>Installation Successful!</h4>
                                <p>The Student Attendance System has been installed successfully.</p>
                                <a href="index.php" class="btn btn-primary">Go to Login</a>
                            </div>
                            <h5 class="mt-4">Installation Summary</h5>
                            <ul class="list-group mb-4">
                                <?php foreach ($steps as $step => $info): ?>
                                    <li class="list-group-item">
                                        <span class="step-indicator <?php echo $info['status']; ?>"></span>
                                        <strong><?php echo ucfirst($step); ?>:</strong> <?php echo ucfirst($info['status']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <h5>Installation Errors</h5>
                                    <ul>
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" action="" class="needs-validation" novalidate>
                                <h4 class="mb-3">Database Configuration</h4>
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_username" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_username" name="db_username" value="root" required>
                                </div>
                                <div class="mb-3">
                                    <label for="db_password" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_password" name="db_password" value="">
                                </div>
                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" value="student_attendance" required>
                                </div>
                                
                                <h4 class="mb-3 mt-4">Admin User Setup</h4>
                                <div class="mb-3">
                                    <label for="admin_username" class="form-label">Admin Username</label>
                                    <input type="text" class="form-control" id="admin_username" name="admin_username" value="admin" required>
                                </div>
                                <div class="mb-3">
                                    <label for="admin_password" class="form-label">Admin Password</label>
                                    <input type="password" class="form-control" id="admin_password" name="admin_password" value="admin" required>
                                    <div class="form-text">Default: admin</div>
                                </div>
                                
                                <h4 class="mb-3 mt-4">Dependencies</h4>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="yes" id="install_composer" name="install_composer">
                                        <label class="form-check-label" for="install_composer">
                                            Install PHP dependencies (PHPMailer, QR Code) using Composer
                                        </label>
                                    </div>
                                    <div class="form-text">This requires Composer to be installed on your server.</div>
                                </div>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" name="install" class="btn btn-primary btn-lg">Install Now</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>