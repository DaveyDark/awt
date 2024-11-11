<div class="col-md-6 col-lg-4" onclick="window.location.href = 'projects/<?= $project['id'] ?>'">
  <div class="card project-card h-100 shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <h5 class="card-title"><?= $project['title'] ?></h5>
        <span class="badge <?php
                            switch ($project['status']) {
                              case 'active':
                                echo 'bg-success';
                                break;
                              case 'in review':
                                echo 'bg-warning';
                                break;
                              case 'completed':
                              case 'submitted':
                                echo 'bg-primary';
                                break;
                              case 'denied':
                                echo 'bg-danger';
                                break;
                            }
                            ?> text-capitalize"><?= $project['status'] ?></span>
      </div>
      <p class="card-text text-muted small mb-2">
        <i class="bi bi-people-fill"></i> Members: <?= $project['members'] ?>
      </p>
      <p class="card-text mb-3 line-clamp-3"><?= $project['description'] ?></p>
      <div class="small text-muted mb-2">
        <?php if (isset($project['assigned']) && $project['status'] === "active") : ?>
          <div>Assigned: <?= $project['assigned'] ?></div>
        <?php endif; ?>
        <?php if (isset($project['due']) && $project['status'] === "active") : ?>
          <div>Due: <?= $project['due'] ?></div>
        <?php endif; ?>
        <?php if (isset($project['submitted']) && $project['status'] === "submitted") : ?>
          <div>Due: <?= $project['submitted'] ?></div>
        <?php endif; ?>
        <?php if (isset($project['completed']) && $project['status'] === "completed") : ?>
          <div>Due: <?= $project['completed'] ?></div>
        <?php endif; ?>
      </div>
      <?php if (isset($project['teacher'])) : ?>
        <div class="text-muted small">Teacher: <?= $project['teacher'] ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
