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
        <td>${student.phone || '-'}</td>
        <td>${student.email || '-'}</td>
        <td>
          <button class="btn btn-warning btn-sm" onclick="openEditStudentModal('${student.urn}')">Edit</button>
          <button class="btn btn-danger btn-sm" onclick="deleteStudent('${student.urn}')">Delete</button>
          <button class="btn btn-success btn-sm" onclick="generateQR('${student.urn}', '${student.name}')">QR</button>
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
// Function to edit a student - called from the Edit button
const openEditStudentModal = async (urn) => {
  try {
    const response = await fetch(`../api/students.php?urn=${urn}`);
    const student = await response.json();

    // Populate the form with student details
    document.getElementById("urn").value = student.urn;
    document.getElementById("name").value = student.name;
    document.getElementById("branch").value = student.branch;
    document.getElementById("phone").value = student.phone || '';
    document.getElementById("email").value = student.email || '';

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

// Function to delete a student - called from the Delete button
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

// Function to generate and download a QR code - called from the QR button
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
