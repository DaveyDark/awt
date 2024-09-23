<?php
session_start();

if (!isset($_SESSION['te_user_id'])) {
  header("Location: ../login");
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Text Editor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
</head>

<body>
  <div class="container py-2">
    <div class="d-flex flex-sm-row flex-column justify-content-between border align-items-center bg-light p-2 rounded shadow-sm mt-3">
      <h1 class="display-5">Text Editor</h1>
      <div class="d-flex flex-sm-row flex-column gap-2 justify-content-end">
        <a href="../api/logout.php" class="btn btn-outline-danger">Logout</a>
      </div>
    </div>

    <!-- Document Management -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center my-3">
      <select id="documentDropdown" class="form-select w-50">
        <option value="">Select Document</option>
      </select>
      <button id="renameDocumentBtn" class="btn btn-warning ms-2">Rename</button>
      <button id="deleteDocumentBtn" class="btn btn-danger ms-2">Delete</button>
      <button id="newDocumentBtn" class="btn btn-success ms-2" data-bs-toggle="modal" data-bs-target="#newDocumentModal">New Document</button>
    </div>

    <!-- Quill Editor -->
    <div id="editor" class="bg-light p-3"></div>

    <!-- Modal for Creating New Document -->
    <div class="modal fade" id="newDocumentModal" tabindex="-1" aria-labelledby="newDocumentModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newDocumentModalLabel">Create New Document</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="newDocumentForm">
              <div class="mb-3">
                <label for="newDocumentName" class="form-label">Document Name</label>
                <input type="text" class="form-control" id="newDocumentName" required>
              </div>

              <button type="submit" class="btn btn-primary">Create Document</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div id="save-alert-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055;"></div>

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script src="../js/home.js"></script>
  </div>
</body>

</html>
