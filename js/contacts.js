// Get elements from page
const contactsTableBody = document.getElementById("contacts-table-body");
const contactForm = document.getElementById("contact-form");
const searchInput = document.getElementById("search-input");
const logoutButton = document.getElementById("logout-button");
const addContactButton = document.getElementById("add-contact-button");
const contactModalElement = document.getElementById("contact-modal");
const contactModalLabel = document.getElementById("contact-modal-label");
const saveContactButton = contactForm.querySelector('button[type="submit"]');
let editingContactID = null;

const storedUserID = sessionStorage.getItem("uID");
const userID = Number(storedUserID);

// redirect anyone who has not logged in
if (storedUserID === null || !Number.isInteger(userID) || userID <= 0) {
    sessionStorage.removeItem("uID");
    window.location.replace("index.html");
} else {
    // load contacts if user exists
    loadContacts("");
}
// Search
async function loadContacts(search) {

    try {
        const response = await fetch(
            "http://192.241.156.39/API/searchContact.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    search: search,
                    uID: Number(userID)
                })
            }
        );

        const data = await response.json();

        // The API returns 400 when no records are found.
        if (!response.ok) {
            if (data.error === "No Records Found") {
                displayContacts([]);
                return;
            }
            console.error(
                "Search failed:",
                data.error
            );
            return;
        }
        displayContacts(data.results);

    } catch (error) {
        console.error(
            "Error loading contacts:",
            error
        );
    }
}

// Display contacts
function displayContacts(contacts) {

    contactsTableBody.innerHTML = "";

    // Empty table
    if (contacts.length === 0) {
        contactsTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No contacts found.
                </td>
            </tr>
        `;
        return;
    }

    contacts.forEach(contact => {

        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${contact.fname}</td>
            <td>${contact.lname}</td>
            <td>${contact.phone}</td>
            <td>${contact.email}</td>
            <td>${contact.company}</td>

            <td class="action-buttons">
                <button
                    class="btn btn-sm btn-outline-primary edit-button"
                    data-id="${contact.cID}">
                    Edit
                </button>

                <button
                    class="btn btn-sm btn-outline-danger delete-button"
                    data-id="${contact.cID}">
                    Delete
                </button>
            </td>
        `;
        contactsTableBody.appendChild(row);
    });
}

// Search bar
searchInput.addEventListener("input", function () {
    const search = searchInput.value.trim();
    loadContacts(search);
});

// Add
contactForm.addEventListener("submit", async function (event) {

    event.preventDefault();
    const fname = document.getElementById("contact-fname").value.trim();
    const lname = document.getElementById("contact-lname").value.trim();
    const phone = document.getElementById("contact-phone").value.trim();
    const email = document.getElementById("contact-email").value.trim();
    const company = document.getElementById("contact-company").value.trim();

    try {
        const response = await fetch(
            "http://192.241.156.39/API/addContact.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    fname: fname,
                    lname: lname,
                    phone: phone,
                    email: email,
                    company: company,
                    uID: Number(userID)
                })
            }
        );

        const data = await response.json();

        if (!response.ok) {
            console.error(
                "Failed to add contact:",
                data.error
            );
            return;
        }

        console.log(
            "Contact added:",
            data.cID
        );

        // Clear form
        contactForm.reset();

        // Reload contacts
        await loadContacts(searchInput.value.trim());

        // Close Bootstrap modal
        const modalElement = document.getElementById("contact-modal");
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }

    } catch (error) {
        console.error(
            "Failed to add contact:",
            error
        );
    }
});

// Edit/Delete buttons
contactsTableBody.addEventListener("click", function (event) {

    const button = event.target;

    // Edit
    if (button.classList.contains("edit-button")) {
        const contactID = Number(button.dataset.id);
        const row = button.closest("tr");
        console.log("Edit contact:", contactID);

        // remember which contact is being edited
        editingContactID = contactID;

        // copy existing contact info into the form
        document.getElementById("contact-fname").value = row.cells[0].textContent.trim();
        document.getElementById("contact-lname").value = row.cells[1].textContent.trim();
        document.getElementById("contact-phone").value = row.cells[2].textContent.trim();
        document.getElementById("contact-email").value = row.cells[3].textContent.trim();
        document.getElementById("contact-company").value = row.cells[4].textContent.trim();

        // change the popup to edit mode
        contactModalLabel.textContent = "Edit Contact";
        saveContactButton.textContent = "Save Changes";

        // open the popup
        const modal = bootstrap.Modal.getOrCreateInstance(contactModalElement);

        modal.show();
    }

    // Delete
    if (button.classList.contains("delete-button")) {
        const contactID = button.dataset.id;
        deleteContact(contactID);
    }
});

// Delete
async function deleteContact(contactID) {

    const confirmed = confirm("Are you sure you want to delete this contact?");

    if (!confirmed) {
        return;
    }
    try {
        const response = await fetch(
            "http://192.241.156.39/API/removeContact.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    cID: Number(contactID),
                    uID: Number(userID)
                })
            }
        );
        const data = await response.json();

        if (!response.ok) {
            console.error(
                "Failed to delete contact:",
                data.error
            );
            return;
        }

        console.log(
            "Contact deleted:",
            data.cID
        );
        // Refresh table
        await loadContacts(searchInput.value.trim());

    } catch (error) {
        console.error(
            "Failed to delete contact:",
            error
        );
    }
}

// Logout
logoutButton.addEventListener("click", function () {
    sessionStorage.removeItem("uID");
    window.location.replace("index.html"); // fixed: make sure user can't use the back button to return to this page
});