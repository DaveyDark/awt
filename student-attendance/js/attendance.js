const studentsTable = document.querySelector("#students-table");
const dateInput = document.querySelector("#date-input");

// Function to get today's date in YYYY-MM-DD format
const getTodayDate = () => {
  const today = new Date();
  const year = today.getFullYear();
  const month = String(today.getMonth() + 1).padStart(2, "0"); // Month is 0-indexed, so we add 1
  const day = String(today.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
};

// Fetch students and attendance for the selected date
const fetchAttendance = async (selectedDate) => {
  try {
    const response = await fetch(`../api/attendance.php?date=${selectedDate}`);
    const data = await response.json();

    studentsTable.innerHTML = ""; // Clear the table before adding rows

    data.forEach((student) => {
      const row = document.createElement("tr");

      // Apply class based on attendance status
      if (student.present === "yes") {
        row.classList.add("table-success"); // Green for present
      } else {
        row.classList.add("table-danger"); // Red for absent
      }

      row.innerHTML = `
        <td>${student.urn}</td>
        <td>${student.name}</td>
        <td>${student.branch}</td>
        <td>${student.present}</td>
      `;
      studentsTable.appendChild(row);
    });
  } catch (error) {
    console.error("Error fetching attendance data:", error);
  }
};

// Set the date input to today's date and fetch attendance on page load
window.addEventListener("DOMContentLoaded", () => {
  const todayDate = getTodayDate();
  dateInput.value = todayDate; // Set today's date in the date input
  fetchAttendance(todayDate); // Fetch attendance for today's date
});

// Listen for changes in the date input field
dateInput.addEventListener("change", (e) => {
  const selectedDate = e.target.value;
  if (selectedDate) {
    fetchAttendance(selectedDate); // Fetch attendance for the selected date
  }
});
