const pendingTable = document.querySelector("#pendingReview");
const completedTable = document.querySelector("#completedReview");
const reviewModal = new bootstrap.Modal(document.getElementById("reviewModal"));
const addStudentModal = new bootstrap.Modal(
  document.getElementById("addStudentModal"),
); // Modal for adding students
const reviewForm = document.querySelector("#reviewForm");
const addStudentForm = document.querySelector("#addStudentForm"); // Form inside Add Student Modal
const nameField = document.querySelector("#review-name");
const rollField = document.querySelector("#review-roll");
const remarksField = document.querySelector("#review-remarks");
const reviewContentGroup = document.querySelector("#review-content-group");
const submitButton = document.querySelector("#submit-button");
const statusGroup = document.querySelector("#status-group");
const logoutButton = document.querySelector("#logout");

// Show Add Student Modal when Add Student button is clicked
if (user_type === "l1") {
  const addStudentButton = document.querySelector("#add-student");
  addStudentButton.addEventListener("click", () => {
    addStudentModal.show(); // Show the modal
  });
}

// Handle Add Student form submission
addStudentForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  // Get the form values
  const formData = new FormData(addStudentForm);

  try {
    // Send the POST request to add the student
    const response = await fetch("../api/students.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error("Failed to add student");
    }

    // Close the modal after successful submission
    addStudentModal.hide();

    // Clear the form
    addStudentForm.reset();

    // Reload the student list
    await fetchData();
  } catch (error) {
    console.error("Error adding student:", error);
  }
});

logoutButton.addEventListener("click", async (e) => {
  await fetch("../api/logout.php");
  window.location.href = "../login";
});

async function fetchData() {
  pendingTable.innerHTML = ""; // Clear the pending table
  completedTable.innerHTML = ""; // Clear the completed table

  const res = await fetch("../api/students.php");
  const data = await res.json();

  for (const student of data) {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${student.name}</td>
      <td>${student.urn}</td>
      <td>${student.status}</td>
    `;
    tr.setAttribute("data-id", student.id);
    tr.setAttribute("data-name", student.name);
    tr.setAttribute("data-roll", student.urn);
    tr.setAttribute("data-remark1", student.remark1);
    tr.setAttribute("data-remark2", student.remark2);
    tr.setAttribute("data-remark3", student.remark3);
    tr.setAttribute("data-remark4", student.remark4);

    if (student.status === "pending") {
      pendingTable.appendChild(tr);
      tr.addEventListener("click", () => openReviewModal(student.id));
    } else {
      completedTable.appendChild(tr);
      tr.addEventListener("click", () => openReviewInfoModal(student.id));
    }
  }
}

async function openReviewModal(id) {
  reviewForm.reset();
  reviewContentGroup.disabled = false;
  reviewForm.setAttribute("data-id", id);
  reviewContentGroup.style.display = "block";
  submitButton.style.display = "block";
  const tr = document.querySelector(`tr[data-id="${id}"]`);
  nameField.value = tr.getAttribute("data-name");
  rollField.value = tr.getAttribute("data-roll");
  remarksField.value = "";
  const remark1 = tr.getAttribute("data-remark1");
  const remark2 = tr.getAttribute("data-remark2");
  const remark3 = tr.getAttribute("data-remark3");
  if (remark1 != "null")
    remarksField.value += "----------------\n" + remark1 + "\n";
  if (remark2 != "null")
    remarksField.value += "----------------\n" + remark2 + "\n";
  if (remark3 != "null") {
    remarksField.value += "----------------\n" + remark3 + "\n";
    statusGroup.style.display = "block";
  } else {
    statusGroup.style.display = "none";
  }
  reviewModal.show();
}

async function openReviewInfoModal(id) {
  reviewForm.reset();
  reviewContentGroup.disabled = true;
  reviewContentGroup.style.display = "none";
  submitButton.style.display = "none";
  statusGroup.style.display = "none";
  reviewForm.setAttribute("data-id", id);
  const tr = document.querySelector(`tr[data-id="${id}"]`);
  nameField.value = tr.getAttribute("data-name");
  rollField.value = tr.getAttribute("data-roll");
  remarksField.value = "";
  const remark1 = tr.getAttribute("data-remark1");
  const remark2 = tr.getAttribute("data-remark2");
  const remark3 = tr.getAttribute("data-remark3");
  const remark4 = tr.getAttribute("data-remark4");
  if (remark1 != "null")
    remarksField.value += "----------------\n" + remark1 + "\n";
  if (remark2 != "null")
    remarksField.value += "----------------\n" + remark2 + "\n";
  if (remark3 != "null")
    remarksField.value += "----------------\n" + remark3 + "\n";
  if (remark4 != "null")
    remarksField.value += "----------------\n" + remark4 + "\n";
  reviewModal.show();
}

reviewForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(reviewForm);
  formData.append("student_id", reviewForm.getAttribute("data-id"));

  try {
    // Send the POST request to add the student
    const response = await fetch("../api/students.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error("Failed to add student");
    }

    // Close the modal after successful submission
    reviewModal.hide();

    // Clear the form
    reviewForm.reset();

    // Reload the student list
    await fetchData();
  } catch (error) {
    console.error("Error reviewing student:", error);
  }
});

fetchData();
