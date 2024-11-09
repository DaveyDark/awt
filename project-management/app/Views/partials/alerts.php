<?php $session = session() ?>
<div class="position-absolute bottom-0 end-0 d-flex gap-1 z-3 flex-column p-4">
  <div class="alert alert-danger" id="validation-alert"></div>
  <?php $error = $session->get('error'); ?>
  <?php if ($error): ?>
    <div class="alert alert-danger">
      <p><?= esc($error) ?></p>
    </div>
  <?php endif ?>
  <?php $success = $session->get('success'); ?>
  <?php if ($success): ?>
    <div class="alert alert-success">
      <p><?= esc($success) ?></p>
    </div>
  <?php endif ?>
  <?php $errors = session()->getFlashdata('errors'); ?>
  <?php if ($errors) : ?>
    <?php foreach ($errors as $field => $error) : ?>
      <div class="alert alert-danger"><?= esc($error) ?></div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
