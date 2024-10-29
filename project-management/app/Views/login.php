<!DOCTYPE html>
<html>

<head>
  <title>Login</title>
  <!-- Bootstrap imports -->
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/global.css" rel="stylesheet">
  <script src="/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="row w-100">
      <div class="col-lg-10 col-xl-9 mx-auto">
        <div class="card flex-row border-0 shadow rounded-3 overflow-hidden">
          <div class="card-left d-none d-md-flex">
            <p class="text-uppercase fs-1 text-center mx-auto fw-bold">Project Management</p>
          </div>
          <div class="card-body p-4 p-sm-5">
            <h5 class="card-title text-center mb-5 fw-light text-uppercase fs-2">Login</h5>
            <form>

              <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInputEmail" placeholder="name@example.com" required autofocus>
                <label for="floatingInputEmail">Email address</label>
              </div>

              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
              </div>

              <div class="d-grid mb-2">
                <button class="btn btn-lg btn-primary btn-login fw-bold text-uppercase" type="submit">Login</button>
              </div>

              <a class="d-block text-center mt-2 small" href="/register"> Don't have an account? Register</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
