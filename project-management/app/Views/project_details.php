<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Details</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/global.css" rel="stylesheet">
  <script src="/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <?php include 'partials/alerts.php' ?>

  <!-- Projects Header -->
  <div class="container py-4 bg-light min-vh-100 px-5 pt-4">
    <?php include 'partials/header.php' ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>
        <?= $project['title'] ?>
      </h2>
    </div>
    <p id="project-description">
      <?= $project['description'] ?>
    </p>
    <div class="row">
      <div class="col-md-6">
        <h2>Project Details</h2>
        <div class="mb-3">
          <label for="status" class="form-label">Status</label>
          <input type="text" class="form-control text-capitalize
              <?php
              switch ($project['status']) {
                case 'active':
                  echo 'bg-success text-light';
                  break;
                case 'in review':
                  echo 'bg-warning';
                  break;
                case 'completed':
                case 'submitted':
                  echo 'bg-primary text-light';
                  break;
                case 'denied':
                  echo 'bg-danger text-light';
                  break;
              } ?>" id="status" value="<?= $project['status'] ?>" readonly>
        </div>

        <div class="row">
          <div class="col mb-3">
            <label for="assigned" class="form-label">Assigned</label>
            <input type="text" class="form-control" id="assigned" value="<?= $project['assigned'] ?? "N/A" ?>" readonly>
          </div>
          <div class=" col mb-3">
            <label for="due" class="form-label">Due</label>
            <input type="text" class="form-control" id="due" value="<?= $project['due'] ?? "N/A" ?>" readonly>
          </div>
        </div>
        <div class="row">
          <div class="col mb-3">
            <label for="submitted" class="form-label">Submitted</label>
            <input type="text" class="form-control" id="submitted" value="<?= $project['submitted'] ?? "N/A" ?>" readonly>
          </div>
          <div class="col mb-3">
            <label for="completed" class="form-label">Completed</label>
            <input type="text" class="form-control" id="completed" value="<?= $project['completed'] ?? "N/A" ?>" readonly>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <h2>Project Members</h2>
        <ul class="list-group" id="project-members">
          <?php foreach ($project['members'] as $member): ?>
            <li class="list-group-item"><?= $member ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="my-3">
          <label for="teacher" class="form-label">Teacher</label>
          <input type="text" class="form-control" id="teacher" value="<?= $project['teacher'] ?>" readonly>
        </div>
        <?php if ($project['status'] === 'in review' && session()->get('role') === "admin"): ?>
          <form method="post" action="/projects/assignTeacher/<?= $project['id'] ?>" class="bg-secondary p-2 rounded">
            <label for="teacherDD" class="form-label text-light">Assign Teacher</label>
            <select name="teacher" id="teacherDD" class="form-control">
              <?php foreach ($teachers as $teacher): ?>
                <option value="<?= $teacher['id'] ?>"><?= $teacher['name'] ?></option>
              <?php endforeach; ?>
            </select>
            <input type="submit" class="form-control mt-1 btn btn-primary" value="Assign" />
          </form>
        <?php endif; ?>
        <?php if ($project['status'] === 'in review' && session()->get('role') === "teacher"): ?>
          <h3>Review Project</h3>
          <div class="my-3">
            <form method="post" action="/projects/approve/<?= $project['id'] ?>" class="d-inline">
              <label for="due" class="form-label">Due Date</label>
              <input type="date" class="form-control" name="due" id="due" />
              <input type="submit" class="btn btn-success" value="Approve" />
            </form>
            <form method="post" action="/projects/deny/<?= $project['id'] ?>" class="d-inline">
              <input type="submit" class="btn btn-danger" value="Deny" />
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($project['status'] === 'completed' || $project['status'] === 'submitted'): ?>
      <h2>Submitted Files</h2>
      <div class="row" id="submitted-files">
        <div class="col-md-6 col-lg-4 mb-3">
          <?php foreach ($project['submissions'] as $file): ?>
            <div class="card">
              <div class="card-body">
                <h5 class="card-title"><?= $file['name'] ?></h5>
                <p class="card-text"><?= $file['created_at'] ?></p>
                <a href="<?= '/projects/download/' . $project['id'] . '/' . $file['id'] ?>" class="btn btn-primary">Download</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if ($project['status'] === 'active' && session()->get('role') === "student"): ?>
      <div class="my-3">
        <h2>Submit Files</h2>
        <form method="post" action="/projects/submit/<?= $project['id'] ?>" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="submission-files" class="form-label">Upload Files</label>
            <input type="file" name="files[]" id="submission-files" class="form-control" multiple required>
          </div>
          <input type="submit" class="btn btn-primary" value="Submit Files" />
        </form>
      </div>
    <?php endif; ?>
    <div class="mt-4">
      <h2>Remarks</h2>
      <div class="mb-3">
        <label for="internal-remarks" class="form-label">Internal Remarks</label>
        <textarea class="form-control" id="internal-remarks" rows="3" readonly>
            <?= $project['internal_remarks'] ?? "N/A" ?>
          </textarea>
      </div>
      <div class="mb-3">
        <label for="external-remarks" class="form-label">External Remarks</label>
        <textarea class="form-control" id="external-remarks" rows="3" readonly>
            <?= $project['external_remarks'] ?? "N/A" ?>
          </textarea>
      </div>
    </div>
  </div>
</body>

</html>
