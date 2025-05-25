const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');
const userTypeSelect = document.getElementById('userType');
const consultantAreaContainer = document.getElementById('consultantAreaContainer');
const form = document.querySelector('form');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

userTypeSelect.addEventListener('change', function() {
    if (this.value === 'consultant') {
        consultantAreaContainer.style.display = 'block';
    } else {
        consultantAreaContainer.style.display = 'none';
    }
});

// Check if email or mobile already exists
form.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const email = document.getElementById('email').value;
    const mobile = document.getElementById('mobile').value;

    // AJAX request to check if email or mobile is already in use
    fetch('check_user.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, mobile }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            alert('Email or Mobile number already in use. Please use a different one.');
        } else {
            // If email and mobile are unique, proceed to register
            form.submit(); // Submit the form
        }
    })
    .catch(error => console.error('Error:', error));
});

const proofUploadContainer = document.getElementById('proofUploadContainer');

// Modify the existing userTypeSelect event listener
userTypeSelect.addEventListener('change', function () {
    if (this.value === 'consultant') {
        consultantAreaContainer.style.display = 'block';
        proofUploadContainer.style.display = 'block'; // Show the upload proof container
    } else {
        consultantAreaContainer.style.display = 'none';
        proofUploadContainer.style.display = 'none'; // Hide the upload proof container
    }
});

form.addEventListener('submit', function (event) {
    // Check if the user is a consultant
    if (userTypeSelect.value === 'consultant') {
        // Validate Area of Expertise
        if (!consultantArea.value) {
            event.preventDefault(); // Prevent form submission
            alert('Please select your Area of Expertise.');
            consultantArea.focus(); // Focus on the dropdown for the user
            return;
        }
    }
});
