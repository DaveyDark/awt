const roll_number = document.getElementById("roll_number");
const feedback = document.getElementById("feedback");
const table = document.getElementById("seatingTable");
const seatBtn = document.getElementById("seatBtn");

function buildSeatingTable(arrangement) {
  document.getElementById("arrangementName").textContent = arrangement.name;
  table.innerHTML = ""; // Clear existing table content

  // Create table rows and columns based on the arrangement data
  for (let row = 1; row <= arrangement.row_count; row++) {
    const tr = document.createElement("tr");

    for (let col = 1; col <= arrangement.column_count; col++) {
      const seat = arrangement.seats.find(
        (s) => s.row_num === row && s.col_num === col,
      );
      const td = document.createElement("td");
      const div = document.createElement("div");
      div.classList.add("seat");
      if (seat.roll_number) div.title = seat.roll_number;

      if (seat.roll_number === null) {
        div.classList.add("free");
      } else {
        div.classList.add("occupied");
      }
      td.appendChild(div);

      tr.appendChild(td);
    }

    table.appendChild(tr);
  }
}

async function fetchData() {
  try {
    const res = await fetch(`../api/arrangements.php?id=${document.page_id}`);
    const data = await res.json();
    buildSeatingTable(data);
  } catch (error) {
    console.log(error);
  }
}

async function seat() {
  if (!roll_number.value) {
    feedback.textContent = "Roll number is required";
    return;
  } else if (typeof parseInt(roll_number.value) !== "number") {
    feedback.textContent = "Invalid roll number";
    return;
  }
  try {
    const res = await fetch("../api/seat.php", {
      method: "POST",
      body: JSON.stringify({
        arrangement_id: document.page_id,
        roll_number: roll_number.value,
      }),
    });
    if (res.status !== 200) {
      const data = await res.json();
      feedback.textContent = data.error;
    } else {
      feedback.textContent = "Student seated successfully";
      fetchData();
    }
  } catch (error) {
    console.log(error);
  }
}

seatBtn.addEventListener("click", () => {
  seat();
});
roll_number.addEventListener("input", () => {
  feedback.textContent = " ";
});
roll_number.addEventListener("keyup", (e) => {
  if (e.key === "Enter") {
    seat();
  }
});

fetchData();
