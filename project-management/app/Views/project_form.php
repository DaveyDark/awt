<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Project</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/global.css" rel="stylesheet">
  <script src="/js/bootstrap.bundle.min.js"></script>
</head>

<body class="bg-light">
  <?php include 'partials/alerts.php' ?>
  <div class="container py-4 bg-light">
    <?php include 'partials/header.php' ?>

    <!-- Create Project Form -->
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title mb-4">Create New Project</h3>
            <form id="projectForm" method="post" action="/projects/create">
              <!-- Title -->
              <div class="mb-3">
                <label for="title" class="form-label">Project Title</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?= old('title') ?>">
              </div>

              <!-- Description -->
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="4" name="description" required></textarea>
              </div>

              <!-- Members Section -->
              <div class="mb-4">
                <label class="form-label">Team Members</label>
                <div class="d-flex gap-2 mb-2">
                  <input type="text" class="form-control" id="memberInput" placeholder="Enter URN, CRN, or Email"
                    name="memberInput" maxlength="50" value="<?= old('memberInput') ?>">
                  <button type="button" class="btn btn-primary" id="addMember">Add Member</button>
                </div>
                <small class="text-muted">Maximum 3 members allowed</small>

                <!-- Members List -->
                <div class="mt-3">
                  <div id="membersList" class="list-group"></div>
                </div>
              </div>

              <!-- Hidden input to hold members JSON -->
              <input type="hidden" name="members" id="membersInput">

              <!-- Submit Button -->
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Create Project</button>
                <a href="/" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    const currentStudent = JSON.parse('<?= json_encode($student) ?>');
  </script>
  <script src="/js/project_form.js"></script>
</body>

</html>
