<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Dashboard</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/global.css" rel="stylesheet">
  <script src="/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
  <div class="container py-4 bg-light min-vh-100 px-5 pt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <p class="mb-0">Welcome</p>
        <p class="h4 mb-0 fw-bold"><?= $name ?></p>
      </div>
      <a href="/logout" class="btn btn-danger fw-bold">Logout</a>
    </div>

    <hr>

    <!-- Projects Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>My Projects</h2>
      <button class="btn btn-primary fw-bold">New Project</button>
    </div>

    <!-- Projects Grid -->
    <div class="row g-4">
      <!-- Project Card 1 -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title">Web Development Portfolio</h5>
              <span class="badge bg-success">Active</span>
            </div>
            <p class="card-text text-muted small mb-2">
              <i class="bi bi-people-fill"></i> Members: 4
            </p>
            <p class="card-text mb-3">Create a responsive portfolio website showcasing student projects and achievements. The website will include multiple sections...</p>
            <div class="small text-muted mb-2">
              <div>Assigned: Oct 1, 2024</div>
              <div>Due: Nov 15, 2024</div>
            </div>
            <div class="text-muted small">Teacher: Ms. Johnson</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
