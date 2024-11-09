const members = new Set();
const memberDetails = new Map(); // To store complete student details

members.add(currentStudent.id);
memberDetails.set(currentStudent.id, currentStudent);

const membersList = document.getElementById("membersList");
const memberInput = document.getElementById("memberInput");
const addMemberBtn = document.getElementById("addMember");
const projectForm = document.getElementById("projectForm");
const validationAlert = document.getElementById("validation-alert");
const membersInput = document.getElementById("membersInput"); // Hidden input for members JSON
validationAlert.style.display = "none";

// Add member to the list
addMemberBtn.addEventListener("click", async () => {
  const identifier = memberInput.value.trim();

  // Validation checks
  if (!identifier) {
    validationAlert.style.display = "block";
    validationAlert.innerText = "Please enter a URN, CRN, or Email";
    return;
  }

  if (members.size >= 3) {
    validationAlert.style.display = "block";
    validationAlert.innerText = "Maximum 3 members allowed";
    return;
  }

  // API call to get student details
  const student = await fetch(`/students/${identifier}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .catch((error) => {
      console.error("Error:", error);
      validationAlert.style.display = "block";
      validationAlert.innerText = "Student not found";
      return null;
    });

  if (!student) return;

  // Check for duplicates
  const isDuplicate = Array.from(members).some((memberId) => {
    const member = memberDetails.get(memberId);
    return (
      member.urn === student.urn ||
      member.crn === student.crn ||
      member.email === student.email
    );
  });

  if (isDuplicate) {
    validationAlert.style.display = "block";
    validationAlert.innerText = "This student is already added";
    return;
  }

  // Add student details to members
  memberDetails.set(student.id, student);
  members.add(student.id);

  // Display the member in the list
  const memberElement = document.createElement("div");
  memberElement.className = "list-group-item";
  memberElement.innerHTML = `
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <div class="fw-bold">${student.name}</div>
        <div class="small text-muted">
          URN: ${student.urn} | CRN: ${student.crn} | Email: ${student.email} | Branch: ${student.branch}
        </div>
      </div>
      <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMember('${student.id}')">Remove</button>
    </div>
  `;
  membersList.appendChild(memberElement);

  memberInput.value = "";
  validationAlert.style.display = "none";
});

// Populate hidden input with members JSON before submitting
projectForm.addEventListener("submit", (e) => {
  // Convert members to an array and then to a JSON string
  const membersArray = Array.from(members);
  membersInput.value = JSON.stringify(membersArray);

  // No need to call preventDefault() since we want a regular form submission
});

// Remove member function
function removeMember(identifier) {
  members.delete(identifier);
  memberDetails.delete(identifier);
  renderMembers();
}

// Render members list
function renderMembers() {
  membersList.innerHTML = "";
  members.forEach((identifier) => {
    const student = memberDetails.get(identifier);
    const memberElement = document.createElement("div");
    memberElement.className = "list-group-item";
    memberElement.innerHTML = `
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-bold">${student.name}</div>
          <div class="small text-muted">
            URN: ${student.urn} | CRN: ${student.crn} | Email: ${student.email} | Branch: ${student.branch}
          </div>
        </div>
        ${
          student.urn === currentStudent.urn
            ? ""
            : `<button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMember('${identifier}')">Remove</button>`
        }
      </div>
    `;
    membersList.appendChild(memberElement);
  });
}

// Input validation for URN
memberInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault();
    addMemberBtn.click();
  }
});

renderMembers();
