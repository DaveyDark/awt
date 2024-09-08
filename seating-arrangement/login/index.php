<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <style>
    body {
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      width: 100%;
      /* Make the card width responsive */
      max-width: 1200px;
      /* Set the maximum width for large screens */
      border: none;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .logo-section {
      background-color: #0d6efd;
      color: #fff;
      padding: 40px;
      text-align: center;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
    }

    .form-section {
      padding: 30px;
    }

    .form-control:focus {
      box-shadow: none;
      border-color: #0d6efd;
    }

    .login-btn {
      width: 100%;
    }
  </style>
</head>

<body>

  <!-- Login Card Start -->
  <div class="card login-card">
    <div class="row g-0">
      <!-- Left Column: Logo Section -->
      <div class="col-md-6 logo-section d-flex align-items-center justify-content-center">
        <h2>Seating Arrangement System</h2>
        <!-- You can replace the text above with an actual logo image -->
        <!-- Example: <img src="your-logo.png" alt="Logo" width="150"> -->
      </div>
      <!-- Right Column: Form Section -->
      <div class="col-md-6 form-section py-5">
        <form action="../api/login.php" method="post">
          <div class="mb-3">
            <label for="username" class="form-label">User Name</label>
            <input type="text" class="form-control" id="name" placeholder="xyz" name="name" required>
          </div>
          <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" placeholder="Your Password" name="password" required>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary login-btn">Sign In</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Login Card End -->

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
