// Get elements from page
const contactsTableBody = document.getElementById("contacts-table-body");
const contactForm = document.getElementById("contact-form");
const searchInput = document.getElementById("search-input");
const logoutButton = document.getElementById("logout-button");
const userID = sessionStorage.getItem("uID");

// Load contacts upon page opening
loadContacts("");

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
        const contactID = button.dataset.id;
        console.log("Edit contact:", contactID);

        // Edit

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
    window.location.href = "index.html";
});