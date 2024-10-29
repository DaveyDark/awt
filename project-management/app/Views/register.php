<!DOCTYPE html>
<html>

<head>
  <title>Register</title>
  <!-- Bootstrap imports -->
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/global.css" rel="stylesheet">
  <script src="/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <!-- Show Validation Errors -->
    <div class="position-absolute bottom-0 end-0 d-flex gap-1 z-3 flex-column p-4">
      <?php $errors = session()->getFlashdata('errors'); ?>
      <?php if (! empty($errors)): ?>
        <?php foreach ($errors as $field => $error): ?>
          <div class="alert alert-danger">
            <p><?= esc($error) ?></p>
          </div>
        <?php endforeach ?>
      <?php endif ?>
    </div>
    <div class="row w-100">
      <div class="col-lg-10 col-xl-9 mx-auto">
        <div class="card flex-row border-0 shadow rounded-3 overflow-hidden">
          <div class="card-left d-none d-md-flex">
            <p class="text-uppercase fs-1 text-center mx-auto fw-bold">Project Management</p>
          </div>
          <div class="card-body p-4 p-sm-5">
            <h5 class="card-title fw-light text-center mb-5 fs-2 text-uppercase">Register</h5>
            <form method="post" action="/register">

              <!-- User Role Selection -->
              <div class="mb-3">
                <label for="roleSelect" class="form-label">Register as:</label>
                <select class="form-select" id="roleSelect" name="role" onchange="toggleFields()">
                  <option value="teacher" <?= old('role') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                  <option value="student" <?= old('role') == 'student' ? 'selected' : '' ?>>Student</option>
                </select>
              </div>

              <!-- Common Fields -->
              <div class="form-floating mb-3">
                <input type="text" name="name" class="form-control" id="floatingInputName" placeholder="myname" required autofocus value="<?= old('name') ?>">
                <label for="floatingInputName">Name</label>
              </div>

              <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="floatingInputEmail" placeholder="name@example.com" value="<?= old('email') ?>">
                <label for="floatingInputEmail">Email address</label>
              </div>

              <hr>

              <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" value="<?= old('password') ?>">
                <label for="floatingPassword">Password</label>
              </div>

              <div class="form-floating mb-3">
                <input type="password" name="confirmPassword" class="form-control" id="floatingPasswordConfirm" placeholder="Confirm Password" value="<?= old('confirmPassword') ?>">
                <label for="floatingPasswordConfirm">Confirm Password</label>
              </div>

              <!-- Student Specific Fields -->
              <div id="studentFields" style="display: none;">

                <hr>
                <span class="d-flex gap-2">
                  <div class="form-floating mb-3 col-6">
                    <input type="text" name="crn" class="form-control" id="floatingCRN" placeholder="CRN" value="<?= old('crn') ?>">
                    <label for="floatingCRN">CRN</label>
                  </div>

                  <div class="form-floating mb-3 col-6">
                    <input type="text" name="urn" class="form-control" id="floatingURN" placeholder="URN" value="<?= old('urn') ?>">
                    <label for="floatingURN">URN</label>
                  </div>
                </span>

                <div class="form-floating mb-3">
                  <select name="branch" id="branch" class="form-control">
                    <option value="CSE" <?= old('branch') == 'CSE' ? 'selected' : '' ?>>CSE</option>
                    <option value="ECE" <?= old('branch') == 'ECE' ? 'selected' : '' ?>>ECE</option>
                    <option value="ME" <?= old('branch') == 'ME' ? 'selected' : '' ?>>ME</option>
                    <option value="CE" <?= old('branch') == 'CE' ? 'selected' : '' ?>>CE</option>
                    <option value="EE" <?= old('branch') == 'EE' ? 'selected' : '' ?>>EE</option>
                    <option value="IT" <?= old('branch') == 'IT' ? 'selected' : '' ?>>IT</option>
                  </select>
                  <label for="floatingBranch">Branch</label>
                </div>
              </div>

              <div class="d-grid mb-2">
                <button class="btn btn-lg btn-primary btn-login fw-bold text-uppercase" type="submit">Register</button>
              </div>

              <a class="d-block text-center mt-2 small" href="/login"> Have an account? Sign In</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function toggleFields() {
      const role = document.getElementById("roleSelect").value;
      const studentFields = document.getElementById("studentFields");
      studentFields.style.display = role === "student" ? "block" : "none";
    }
    // Call toggleFields once on page load in case of form errors
    window.onload = toggleFields;
  </script>
</body>

</html>
