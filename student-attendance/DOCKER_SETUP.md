# Docker Setup Guide for Student Attendance System

This guide provides instructions for setting up the Student Attendance System in a Docker environment.

## Prerequisites

- Docker installed on your system
- Docker Compose (optional, but recommended)
- Basic understanding of Docker volumes and permissions

## Setup Instructions

### 1. File Permissions

Docker containers typically run with a different user than the host system, which can cause permission issues. Here's how to address this:

```bash
# From your host machine, navigate to the project directory
cd /path/to/student-attendance

# Set permissions that allow the container's www-data user to write to necessary directories
mkdir -p temp_qr_codes
chmod -R 777 temp_qr_codes
chmod 666 config/database.php  # Allow the container to modify the DB config
chmod 666 config/mail.php      # Allow the container to modify the mail config
```

### 2. Database Configuration

Since the automated installer may not work in Docker, manually configure the database:

1. Edit the `config/database.php` file:

```php
<?php
return [
    "host" => "db",         // Use container name from docker-compose
    "username" => "root",   // Database username
    "password" => "your_mysql_root_password", // Database password
    "database" => "student_attendance", // Database name
    "charset" => "utf8mb4", // Character set
    "port" => 3306,         // Database port
];
```

2. Create the database and tables manually:

```bash
# Connect to the MySQL container
docker exec -it your_mysql_container bash

# Access MySQL
mysql -u root -p

# Then run:
CREATE DATABASE student_attendance;
USE student_attendance;

# Create tables
CREATE TABLE IF NOT EXISTS Admins (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  deleted BOOLEAN NOT NULL DEFAULT FALSE,
  createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Students (
  urn VARCHAR(7) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  branch VARCHAR(10) NOT NULL,
  phone VARCHAR(15) NULL,
  email VARCHAR(255) NULL,
  deleted BOOLEAN NOT NULL DEFAULT FALSE,
  createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Attendance (
  id INT PRIMARY KEY AUTO_INCREMENT,
  urn VARCHAR(7) NOT NULL,
  date DATE NOT NULL,
  createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (urn) REFERENCES Students(urn)
);

# Add admin user (password: admin)
INSERT INTO Admins (name, password) VALUES ('admin', '$2y$10$6I6CdVQTv7s5K.K1XcL8NeAlyR3tP7l4afkBRJJeI5YyUCR7HyK12');
```

### 3. Composer Dependencies

Install PHP dependencies inside the container:

```bash
# Enter your PHP container
docker exec -it your_php_container bash

# Navigate to the project directory
cd /path/to/mounted/student-attendance

# Install dependencies using composer.json
composer install

# Or install required libraries directly
composer require phpmailer/phpmailer endroid/qr-code
```

If Composer isn't available in the container, you can install it:

```bash
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

### 4. Example Docker Compose File

Here's a sample `docker-compose.yml` file for the project:

```yaml
version: '3'

services:
  web:
    image: php:7.4-apache
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
    depends_on:
      - db
    environment:
      - PHP_MEMORY_LIMIT=256M
    command: bash -c "docker-php-ext-install pdo pdo_mysql && apache2-foreground"

  db:
    image: mysql:5.7
    ports:
      - "3306:3306"
    environment:
      MYSQL_ROOT_PASSWORD: your_password
      MYSQL_DATABASE: student_attendance
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data:
```

### 5. Importing Student Data

To import student data from the CSV file:

```bash
# Copy your CSV file to the container
docker cp students.csv your_php_container:/var/www/html/student-attendance/

# Execute the import script
docker exec -it your_php_container php /var/www/html/student-attendance/scripts/import_csv.php
```

### 6. Troubleshooting Docker-specific Issues

- **Permission denied errors**: Ensure the web server user (www-data) has write permissions to relevant directories
- **Connection refused to database**: Make sure to use the service name as the hostname
- **Cannot send emails**: Docker containers may have limited outbound connectivity; check your network setup
- **Volume mounting issues**: Use absolute paths in your Docker Compose file for reliable mounting
- **PHP dependencies errors**: If you encounter "unexpected token 'use'" errors, make sure you've installed the required Composer dependencies
- **CA certificates errors**: If seeing certificate errors when installing Composer, run `apt-get update && apt-get install -y ca-certificates` in your container

## Accessing the Application

Once set up, you can access the application at:

```
http://localhost:8080/student-attendance/
```

Default login:
- Username: admin
- Password: admin