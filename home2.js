document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById("popup");
    const closeCaseButtons = document.querySelectorAll(".close-case");

    // Function to fetch and display users in the popup
    function fetchUsers() {
        fetch("fetch_users.php")
            .then(response => response.json())
            .then(data => {
                const tableBody = popup.querySelector("table tbody");
                tableBody.innerHTML = ""; // Clear previous entries

                if (data.length > 0) {
                    data.forEach(user => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                            <td>${user.name}</td>
                            <td>${user.mobile}</td>
                            <td>${user.email}</td>
                            <td><button class="close-case" data-user-id="${user.id}">Close</button></td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement("tr");
                    row.innerHTML = "<td colspan='4'>No booked users found.</td>";
                    tableBody.appendChild(row);
                }
            })
            .catch(error => console.error('Error fetching users:', error));
    }

    // Show Popup
    function showPopup() {
        fetchUsers(); // Fetch users before showing popup
        popup.style.display = "flex";
    }

    // Close Popup
    function closePopup() {
        popup.style.display = "none";
    }

    // Handle closing case
    popup.addEventListener("click", function(event) {
        if (event.target.classList.contains("close-case")) {
            const userId = event.target.dataset.userId;
            fetch("close_case.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh the list of users after closing the case
                    fetchUsers();
                } else {
                    console.error("Error closing case:", data.error);
                }
            })
            .catch(error => console.error('Error closing case:', error));
        }
    });

    // Attach event listeners to buttons
    document.querySelector("button[onclick='showPopup()']").addEventListener("click", showPopup);
    popup.querySelector("button[onclick='closePopup()']").addEventListener("click", closePopup);
});

const userIcon = document.getElementById('user-icon');
const dropdownMenu = document.getElementById('dropdown-menu');

userIcon.addEventListener('click', function(event) {
    event.stopPropagation(); // Prevent the click from bubbling up to the window
    if (dropdownMenu.style.display === "block") {
        dropdownMenu.style.display = "none";
    } else {
        dropdownMenu.style.display = "block";
    }
});

// Close dropdown if clicked outside
window.addEventListener('click', function(event) {
    if (!event.target.matches('.user-icon')) {
        dropdownMenu.style.display = "none";
    }
});
