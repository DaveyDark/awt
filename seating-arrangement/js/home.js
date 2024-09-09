async function fetchData() {
  try {
    const res = await fetch("../api/arrangements.php");
    const data = await res.json();
    const table = document.querySelector("#arrangements-table tbody");
    for (const arrangement of data) {
      const tr = document.createElement("tr");
      const name = document.createElement("td");
      const size = document.createElement("td");
      name.textContent = arrangement.name;
      size.textContent = arrangement.row_count + "x" + arrangement.column_count;
      tr.appendChild(name);
      tr.appendChild(size);
      tr.addEventListener("click", () => {
        window.location.href = `../seating?id=${arrangement.id}`;
      });
      table.appendChild(tr);
    }
  } catch (error) {
    console.log(error);
  }
}

fetchData();
