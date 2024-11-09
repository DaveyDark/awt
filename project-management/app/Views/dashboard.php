<!DOCTYPE html>
<html lang="en">

<?php
$session = session();
$role = $session->get('role');
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Dashboard</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/global.css" rel="stylesheet">
  <script src="/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
  <?php include 'partials/alerts.php' ?>

  <div class="container py-4 bg-light min-vh-100 px-5 pt-4">
    <?php include 'partials/header.php' ?>
    <!-- Projects Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>My Projects</h2>
      <?php if ($role === 'student') : ?>
        <a href="/projects/new" class="btn btn-primary fw-bold">New Project</a>
      <?php endif; ?>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button"
          role="tab" aria-controls="all" aria-selected="true">All</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button"
          role="tab" aria-controls="active" aria-selected="false">Active</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="in-review-tab" data-bs-toggle="tab" data-bs-target="#in-review" type="button"
          role="tab" aria-controls="in-review" aria-selected="false">In Review</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button"
          role="tab" aria-controls="completed" aria-selected="false">Completed</button>
      </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="projectTabsContent">
      <!-- All Projects Tab -->
      <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <?php if (empty($projects)) : ?>
          <div class="d-flex align-items-center justify-content-center min-vh-50">
            <p class="text-muted">No Projects Found</p>
          </div>
        <?php else : ?>
          <div class="row g-4">
            <?php foreach ($projects as $project) : ?>
              <?php include 'partials/project_card.php'; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Active Projects Tab -->
      <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
        <?php
        $activeProjects = array_filter($projects, fn($project) => $project['status'] === 'active');
        ?>
        <?php if (empty($activeProjects)) : ?>
          <div class="d-flex align-items-center justify-content-center min-vh-50">
            <p class="text-muted">No Projects Found</p>
          </div>
        <?php else : ?>
          <div class="row g-4">
            <?php foreach ($activeProjects as $project) : ?>
              <?php include 'partials/project_card.php'; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- In Review Projects Tab -->
      <div class="tab-pane fade" id="in-review" role="tabpanel" aria-labelledby="in-review-tab">
        <?php
        $inReviewProjects = array_filter($projects, fn($project) => $project['status'] === 'in review');
        ?>
        <?php if (empty($inReviewProjects)) : ?>
          <div class="d-flex align-items-center justify-content-center min-vh-50">
            <p class="text-muted">No Projects Found</p>
          </div>
        <?php else : ?>
          <div class="row g-4">
            <?php foreach ($inReviewProjects as $project) : ?>
              <?php include 'partials/project_card.php'; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Completed Projects Tab -->
      <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
        <?php
        $completedProjects = array_filter($projects, fn($project) => $project['status'] === 'completed');
        ?>
        <?php if (empty($completedProjects)) : ?>
          <div class="d-flex align-items-center justify-content-center min-vh-50">
            <p class="text-muted">No Projects Found</p>
          </div>
        <?php else : ?>
          <div class="row g-4">
            <?php foreach ($completedProjects as $project) : ?>
              <?php include 'partials/project_card.php'; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</body>

</html>
