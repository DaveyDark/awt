const studentsTable = document.querySelector("#students-table");
const studentForm = document.querySelector("#studentForm");
const studentModalLabel = document.querySelector("#studentModalLabel");
const saveStudentBtn = document.querySelector("#saveStudentBtn");
const studentModal = new bootstrap.Modal(
  document.getElementById("studentModal"),
);

// Fetch students from the API and display them
const fetchStudents = async () => {
  try {
    const response = await fetch("../api/students.php");
    const students = await response.json();
    studentsTable.innerHTML = ""; // Clear table before adding rows
    students.forEach((student) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${student.urn}</td>
        <td>${student.name}</td>
        <td>${student.branch}</td>
        <td>
          <button class="btn btn-warning" onclick="editStudent(${student.id})">Edit</button>
          <button class="btn btn-danger" onclick="deleteStudent(${student.id})">Delete</button>
          <button class="btn btn-success" onclick="generateQR('${student.urn}', '${student.name}')">Get QR</button>
        </td>
      `;
      studentsTable.appendChild(row);
    });
  } catch (error) {
    console.error(error);
  }
};

// Open modal for adding a new student
document.getElementById("add-student-btn").addEventListener("click", () => {
  studentForm.reset(); // Clear form
  document.getElementById("urn").value = ""; // Ensure the hidden urn field is cleared
  studentModalLabel.innerText = "Add Student"; // Change modal title
  saveStudentBtn.innerText = "Add Student"; // Change button text
});

// Open modal for editing an existing student
const openEditStudentModal = async (urn) => {
  try {
    const response = await fetch(`../api/students.php?urn=${urn}`);
    const student = await response.json();

    // Populate the form with student details
    document.getElementById("urn").value = student.urn;
    document.getElementById("name").value = student.name;
    document.getElementById("branch").value = student.branch;
    document.getElementById("urn").value = student.urn;

    // Update modal title and button text
    studentModalLabel.innerText = "Edit Student";
    saveStudentBtn.innerText = "Save Changes";

    // Show the modal
    studentModal.show();
  } catch (error) {
    console.error(error);
  }
};

// Handle form submission for both adding and editing students
studentForm.addEventListener("submit", async (event) => {
  event.preventDefault(); // Prevent form from submitting the traditional way

  const urn = document.getElementById("urn").value;
  const method = urn ? "PUT" : "POST"; // Use PUT for editing, POST for adding

  const formData = new FormData(studentForm);
  const data = Object.fromEntries(formData.entries());

  try {
    const response = await fetch(`../api/students.php`, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    if (response.ok) {
      fetchStudents(); // Refresh the students table
      studentModal.hide(); // Hide the modal after saving
    } else {
      console.error("Error saving student data");
    }
  } catch (error) {
    console.error("Error:", error);
  }
});

// Function to delete a student
const deleteStudent = async (urn) => {
  if (confirm("Are you sure you want to delete this student?")) {
    try {
      const response = await fetch(`../api/students.php`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ urn: urn }),
      });

      if (response.ok) {
        fetchStudents(); // Refresh the table
      } else {
        console.error("Error deleting student");
      }
    } catch (error) {
      console.error(error);
    }
  }
};

const generateQR = (urn, name) => {
  const qrCanvas = document.createElement("canvas"); // Create a canvas to render the QR code

  QRCode.toCanvas(qrCanvas, urn, function (error) {
    if (error) {
      console.error(error);
      return;
    }

    // Create a download link for the QR code
    const downloadLink = document.createElement("a");
    downloadLink.href = qrCanvas.toDataURL("image/png"); // Convert canvas to image URL
    downloadLink.download = `${name}-QR.png`; // Set the download filename
    downloadLink.click(); // Trigger the download
  });
};

// Fetch the students on page load
fetchStudents();
