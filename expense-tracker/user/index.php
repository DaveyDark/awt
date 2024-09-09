<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container p-5">
        <h2>Register a New User</h2>
        <form id="registerForm">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="type">User Type:</label>
                <select class="form-control" name="type" id="type">
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>

        <h1>Users List</h1>
    <table  class="table table-striped table-bordered table-responsive my-3">
    <thead>
            <tr>
              <th>Name</th>
              <th>Type</th>
            </tr>

          </thead>
          <tbody id="usersTable">
          </tbody>
    </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../js/dashboard.js"></script>
</body>
</html>
