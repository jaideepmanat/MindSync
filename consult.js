// Toggle dropdown visibility
document.getElementById('user-icon').addEventListener('click', function () {
    document.getElementById('dropdown-menu').classList.toggle('show');
});

function bookConsultant(consultantName) {
    fetch("book_consultation.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "consultantName=" + encodeURIComponent(consultantName),
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            alert("Booking successful!");

            // Disable the booked button on the page
            document.querySelector(`button[onclick="bookConsultant('${consultantName}')"]`).innerText = 'Booked';
            document.querySelector(`button[onclick="bookConsultant('${consultantName}')"]`).disabled = true;
        } else {
            alert("Failed to book the consultant: " + data);
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
}




let currentConsultantId; // Global variable to hold the current consultant's ID

function rateConsultant(name, id) {
    document.getElementById("consultant-name").innerText = name; // Set the consultant's name in the popup
    currentConsultantId = id; // Set the consultant ID
    document.getElementById("rate-popup").style.display = "block"; // Show the popup
}

function closePopup() {
    document.getElementById("rate-popup").style.display = "none"; // Hide the popup
}

function submitRating() {
    const rating = document.querySelector('input[name="rating"]:checked');
    if (!rating) {
        alert("Please select a rating.");
        return;
    }

    const ratingValue = rating.value;

    // Send the rating to the server via AJAX
    fetch('submit_rating.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ consultantId: currentConsultantId, rating: ratingValue })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the displayed average rating on the frontend
            document.getElementById(`avg-rating-${currentConsultantId}`).innerHTML = getStarRating(data.newAverage);
            closePopup(); // Close the rating popup
        } else {
            alert("Error submitting rating: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
}

function getStarRating(average) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= average ? '★' : '☆';
    }
    return stars;
}



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

// Function to toggle message form visibility
function toggleMessageForm(consultantId) {
    const form = document.getElementById(`message-form-${consultantId}`);
    form.style.display = form.style.display === "none" ? "block" : "none";
}

// Function to submit the message
function submitMessage(consultantId) {
    const messageInput = document.getElementById(`message-input-${consultantId}`);
    const messageText = messageInput.value.trim();

    if (!messageText) {
        alert("Please enter a message.");
        return;
    }

    // Send message data to the server via AJAX
    fetch("submit_message.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `consultant_id=${consultantId}&message_text=${encodeURIComponent(messageText)}`,
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            alert("Message sent successfully!");
            toggleMessageForm(consultantId); // Hide the message form after submission
            messageInput.value = ""; // Clear the input field
        } else {
            alert("Failed to send message: " + data);
        }
    })
    .catch(error => console.error("Error:", error));
}

// Function to cancel the message form
function cancelMessage(consultantId) {
    toggleMessageForm(consultantId); // Hide the form
    document.getElementById(`message-input-${consultantId}`).value = ""; // Clear the input
}
