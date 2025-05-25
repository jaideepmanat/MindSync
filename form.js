// Dropdown Toggle on Click
const userIcon = document.getElementById('user-icon');
const dropdownMenu = document.getElementById('dropdown-menu');

userIcon.addEventListener('click', function(event) {
    event.stopPropagation(); // Prevent the click from bubbling up to the window
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
});

// Close dropdown if clicked outside
window.addEventListener('click', function(event) {
    if (!event.target.matches('.user-icon')) {
        dropdownMenu.style.display = "none";
    }
});
