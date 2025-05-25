document.addEventListener("DOMContentLoaded", function() {
    const popup = document.getElementById("popup");

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
                            <td>
                                <button class="close-case" data-user-id="${user.id}">Close</button>
                            </td>
                            <td>
                                <button class="give-feedback" data-user-id="${user.id}">Give Feedback</button>
                                <div class="feedback-popup" style="display: none;">
                                    <textarea placeholder="Enter feedback here..."></textarea>
                                    <button class="submit-feedback">Submit</button>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } else {
                    const row = document.createElement("tr");
                    row.innerHTML = "<td colspan='5'>No booked users found.</td>";
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

    // Handle showing and hiding the feedback form
    popup.addEventListener("click", function(event) {
        if (event.target.classList.contains("give-feedback")) {
            const button = event.target;
            const userId = button.dataset.userId;
            const feedbackPopup = button.nextElementSibling;  // The div that contains the textarea and submit button

            // Toggle the visibility of the feedback popup
            if (feedbackPopup.style.display === "none" || feedbackPopup.style.display === "") {
                feedbackPopup.style.display = "block";  // Show the feedback form
                button.textContent = "Cancel Feedback";  // Change button text to 'Cancel Feedback'
            } else {
                feedbackPopup.style.display = "none";  // Hide the feedback form
                button.textContent = "Give Feedback";  // Reset button text to 'Give Feedback'
            }
        }

        // Handle submitting the feedback
        if (event.target.classList.contains("submit-feedback")) {
            const feedbackText = event.target.previousElementSibling.value.trim();
            const feedbackRow = event.target.closest("td").parentElement;
            const userId = feedbackRow.querySelector(".give-feedback").dataset.userId;

            if (feedbackText) {
                submitFeedback(userId, feedbackRow, feedbackText);
            } else {
                alert("Feedback cannot be empty.");
            }
        }

        // Handle closing the case (same as before)
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
                    fetchUsers();  // Refresh the list of users after closing the case
                } else {
                    console.error("Error closing case:", data.error);
                }
            })
            .catch(error => console.error('Error closing case:', error));
        }
    });

    // Attach event listener to show the popup
    document.querySelector("button[onclick='showPopup()']").addEventListener("click", showPopup);
    popup.querySelector("button[onclick='closePopup()']").addEventListener("click", closePopup);
});

// Function to submit feedback
function submitFeedback(userId, feedbackRow, feedbackText) {
    fetch("submit_feedback.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `user_id=${userId}&feedback_text=${encodeURIComponent(feedbackText)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Feedback submitted successfully.");
            feedbackRow.querySelector(".give-feedback").textContent = "Give Feedback"; // Reset button text
            feedbackRow.querySelector(".feedback-popup").style.display = "none"; // Hide feedback form
        } else {
            console.error("Error submitting feedback:", data.error);
        }
    })
    .catch(error => console.error('Error submitting feedback:', error));
}

// Dropdown Toggle on Click
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
