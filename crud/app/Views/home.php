<!DOCTYPE html>
<html>

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>CI4 Todo List</title>
</head>

<body class="container pt-4">
  <h1>Todo List</h1>
  <!-- Display alerts on bottom right -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <?php if (session()->has('success')): ?>
      <div class="alert alert-success fade show">
        <?= session('success') ?>
      </div>
    <?php endif; ?>
    <?php if (session()->has('errors')): ?>
      <?php foreach (session('errors') as $error): ?>
        <div class="alert alert-danger fade show">
          <?= $error ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <form action="/create" method="post" class="mb-3 p-2">
    <div class="row">
      <div class="form-group col-10">
        <label for="task" class="mb-1">Task:</label>
        <input type="text" name="task" id="task" class="form-control" required autocomplete="off">
      </div>
      <input type="submit" value="Add Task" class="btn btn-primary mt-2 col-2">
    </div>
  </form>
  <table class="table table-striped table-responsive table-bordered">
    <thead>
      <tr>
        <th>Task</th>
        <th>Created At</th>
        <th>Done</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($todos as $todo): ?>
        <tr>
          <td><?= $todo['task'] ?></td>
          <td><?= $todo['created_at'] ?></td>
          <td class="text-center">
            <input type="checkbox" <?= $todo['done'] ? 'checked' : '' ?> class="toggle" data-id="<?= $todo['id'] ?>">
          </td>
          <td class="text-center">
            <a href="/delete/<?= $todo['id'] ?>" class="btn btn-danger">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script>
    document.querySelectorAll('.toggle').forEach(toggle => {
      toggle.addEventListener('change', async () => {
        try {
          const id = toggle.getAttribute('data-id');
          const response = await fetch(`/toggle/${id}`);
          if (!response.ok) {
            throw new Error('Failed to toggle task');
            toggle.checked = !toggle.checked;
          }
        } catch (error) {
          console.error(error);
          alert('Failed to toggle task');
        }
      });
    });
  </script>
</body>

</html>
