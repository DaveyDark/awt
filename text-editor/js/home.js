let timeoutId;

// Initialize Quill editor
const quill = new Quill("#editor", {
  theme: "snow",
});

// Document management elements
const documentDropdown = document.getElementById("documentDropdown");
const renameDocumentBtn = document.getElementById("renameDocumentBtn");
const deleteDocumentBtn = document.getElementById("deleteDocumentBtn");
const newDocumentBtn = document.getElementById("newDocumentBtn");
const newDocumentForm = document.getElementById("newDocumentForm");
const newDocumentModal = new bootstrap.Modal(
  document.getElementById("newDocumentModal"),
  {
    keyboard: true,
  },
);
// Track the currently selected document
let currentDocumentId = null;

// Fetch and populate documents in the dropdown
const fetchDocuments = async () => {
  try {
    const response = await fetch("../api/documents.php");
    const documents = await response.json();

    documentDropdown.innerHTML = ""; // Clear the dropdown

    if (documents.length === 0) {
      // No documents found, create a new "Untitled" document
      const newDocument = await createUntitledDocument();
      currentDocumentId = newDocument.id;
      const option = document.createElement("option");
      option.value = newDocument.id;
      option.textContent = newDocument.name;
      documentDropdown.appendChild(option);
      quill.setContents(""); // Clear editor for the new document
    } else {
      // Documents found, select the first document
      documents.forEach((doc, index) => {
        const option = document.createElement("option");
        option.value = doc.id;
        option.textContent = doc.name;
        documentDropdown.appendChild(option);

        // Select the first document automatically
        if (index === 0) {
          currentDocumentId = doc.id;
          loadDocumentContent(doc.id); // Load the content of the first document
        }
      });
    }
  } catch (error) {
    console.error("Error fetching documents:", error);
  }
};

// Function to create a new "Untitled" document
const createUntitledDocument = async () => {
  try {
    const response = await fetch("../api/documents.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        name: "Untitled",
        content: "",
      }),
    });

    if (response.ok) {
      const newDocument = await response.json();
      return newDocument;
    } else {
      console.error("Error creating 'Untitled' document");
    }
  } catch (error) {
    console.error("Error creating 'Untitled' document:", error);
  }
};

// Load a document's content into the Quill editor
const loadDocumentContent = async (docId) => {
  try {
    const response = await fetch(`../api/documents.php?id=${docId}`);
    const document = await response.json();

    if (document && document.content) {
      try {
        quill.setContents(JSON.parse(document.content)); // Load rich text content (Delta format)
      } catch (error) {
        quill.setText(document.content); // Fallback to plain text if Delta parsing fails
      }
    } else {
      quill.setContents(""); // Clear editor if no content
    }
  } catch (error) {
    console.error("Error loading document content:", error);
  }
};

// Initialize: Fetch all documents when the page loads
fetchDocuments();

// Load a document's content when selected
documentDropdown.addEventListener("change", async (event) => {
  const docId = event.target.value;
  if (docId) {
    currentDocumentId = docId;
    try {
      const response = await fetch(`../api/documents.php?id=${docId}`);
      const document = await response.json();

      if (document && document.content) {
        // Use setText to load plain text into the editor
        quill.setContents(document.content);
      } else {
        quill.setText(""); // Clear editor if no content
      }
    } catch (error) {
      console.error("Error loading document content:", error);
    }
  } else {
    currentDocumentId = null;
    quill.setText(""); // Clear editor if no document is selected
  }
});

// Handle new document creation
newDocumentForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const newDocumentName = document.getElementById("newDocumentName").value;

  try {
    const response = await fetch("../api/documents.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        name: newDocumentName,
        content: "",
      }),
    });

    if (response.ok) {
      const newDocument = await response.json();
      fetchDocuments(); // Refresh documents dropdown
      document.getElementById("newDocumentName").value = ""; // Clear the form
      newDocumentModal.hide(); // Programmatically hide the modal after creation
      currentDocumentId = newDocument.id;
      quill.setContents(""); // Clear the editor for the new document
    } else {
      console.error("Error creating new document");
    }
  } catch (error) {
    console.error("Error creating new document:", error);
  }
});

// Handle document rename
renameDocumentBtn.addEventListener("click", async () => {
  if (!currentDocumentId) {
    alert("Please select a document to rename.");
    return;
  }

  const newName = prompt("Enter new document name:");
  if (newName) {
    try {
      const response = await fetch("../api/documents.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          id: currentDocumentId,
          name: newName,
          content: quill.getText(),
        }),
      });

      if (response.ok) {
        fetchDocuments(); // Refresh documents dropdown
      } else {
        console.error("Error renaming document");
      }
    } catch (error) {
      console.error("Error renaming document:", error);
    }
  }
});

// Handle document deletion
deleteDocumentBtn.addEventListener("click", async () => {
  if (!currentDocumentId) {
    alert("Please select a document to delete.");
    return;
  }

  if (confirm("Are you sure you want to delete this document?")) {
    try {
      const response = await fetch("../api/documents.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id: currentDocumentId }),
      });

      if (response.ok) {
        currentDocumentId = null; // Clear selected document
        quill.setContents(""); // Clear the editor
        fetchDocuments(); // Refresh documents dropdown
      } else {
        console.error("Error deleting document");
      }
    } catch (error) {
      console.error("Error deleting document:", error);
    }
  }
});

const showSaveAlert = () => {
  const alertContainer = document.getElementById("save-alert-container");

  // Create the alert element
  const alert = document.createElement("div");
  alert.className = "alert alert-success alert-dismissible fade show";
  alert.role = "alert";
  alert.innerHTML = `
        Changes saved successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

  // Append the alert to the container
  alertContainer.appendChild(alert);

  // Automatically remove the alert after 3 seconds
  setTimeout(() => {
    alert.classList.remove("show");
    setTimeout(() => alert.remove(), 150); // Wait for fade-out transition
  }, 3000);
};

// Function to save document content (using Delta format)
const saveDocumentContent = async () => {
  if (!currentDocumentId) return; // No document selected

  try {
    const response = await fetch("../api/documents.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: currentDocumentId,
        name: documentDropdown.options[documentDropdown.selectedIndex].text,
        content: JSON.stringify(quill.getContents()), // Save rich text content (Delta format)
      }),
    });

    if (response.ok) {
      showSaveAlert(); // Show alert when changes are successfully saved
    } else {
      console.error("Error saving document");
    }
  } catch (error) {
    console.error("Error saving document:", error);
  }
};

// Debounce the save operation to delay until the user stops typing for 2 seconds
quill.on("text-change", () => {
  clearTimeout(timeoutId); // Clear the previous timeout
  timeoutId = setTimeout(saveDocumentContent, 1000); // Set a new timeout for 2 seconds
});
// Initialize: Fetch all documents when the page loads
fetchDocuments();
