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

// Function to book a consultant
function bookConsultant(consultantName, consultantId) {
    // Get the user ID from the session
    const userId = <?php echo $_SESSION['user_id']; ?>;

    // Disable the "Book Now" button to prevent multiple clicks
    const bookButton = document.getElementById("book-btn-" + consultantId);
    bookButton.disabled = true;
    bookButton.innerHTML = "Booking...";

    // Send AJAX request to book the consultant
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "book_consultation.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = xhr.responseText;
            if (response === "success") {
                // Change the button text to show that the booking was successful
                bookButton.innerHTML = "Booked";
            } else {
                // If booking failed, re-enable the button and show an error message
                alert("Error: " + response);
                bookButton.disabled = false;
                bookButton.innerHTML = "Book Now";
            }
        }
    };
    xhr.send("consultantName=" + encodeURIComponent(consultantName));
}
