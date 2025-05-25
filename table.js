// Dropdown Toggle on Click
const userIcon = document.getElementById('user-icon');
const dropdownMenu = document.getElementById('dropdown-menu');

// Toggle dropdown menu on user icon click
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

// Apply styles for the status column after the page loads
document.addEventListener("DOMContentLoaded", function() {
    const statusCells = document.querySelectorAll("tbody td:nth-child(2)");

    // Loop through each status cell and apply color based on its value
    statusCells.forEach(cell => {
        if (cell.textContent.trim() === "Active") {
            cell.style.color = "#00ff00"; // Green color for Active
        } else if (cell.textContent.trim() === "Closed") {
            cell.style.color = "#ed4343"; // Red color for Closed
        }
    });
});
