<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Expense Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </head>
  <body class="container">
    <nav>
      <h1 class="mb-4">Expense Tracker</h1>
      <div class="row gap-4">
        <div class="border bg-light rounded-4 p-4 col">
          <h2 class="fs-5 fw-bold">Months:</h2>
          <ul class="pagination px-3 py-1" id="pagination"></ul>
          <div class="row">
            <span class="col-5">
              <input
                class="form-control"
                type="text"
                id="item-name"
                placeholder="Item Name"
              />
            </span>
            <span class="col-5">
              <input
                class="form-control"
                type="number"
                id="item-amount"
                placeholder="Item Amount"
              />
            </span>
            <span class="col-2">
              <button class="btn btn-primary" id="add-item">Add</button>
            </span>
          </div>
          <table
            class="table table-striped table-bordered my-3"
            id="items-table"
          >
            <thead>
              <tr>
                <th>Item</th>
                <th>Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Item 1</td>
                <td>100</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="border bg-light rounded-4 p-4 col">Graph</div>
      </div>
    </nav>

    <script src="js/index.js"></script>
  </body>
</html>