// Motivational Quotes
const quotes = [
    "Believe you can and you're halfway there.",
    "Your limitation—it’s only your imagination.",
    "Push yourself, because no one else is going to do it for you.",
    "Success doesn’t just find you. You have to go out and get it.",
    "Great things never come from comfort zones."
];

window.onload = function() {
    const quoteElement = document.getElementById('quote');
    const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
    quoteElement.textContent = randomQuote;
};

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



