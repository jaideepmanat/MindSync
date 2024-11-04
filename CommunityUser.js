// Function to submit a new question via AJAX
function submitQuestion() {
    var questionText = document.getElementById('userQuestion').value;
    
    // Check if the question text is empty
    if (questionText === "") {
        alert("Please enter a question before submitting.");
        return;
    }

    // Initialize the AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "submit_question.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    
    // Callback function to handle the response
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            
            // If submission is successful, add the new question to the page
            if (response.success) {
                var newQuestion = document.createElement('div');
                newQuestion.className = 'question';
                newQuestion.innerHTML = `
                    <h3>${questionText}</h3>
                    <div class="reply">
                        <p>Thank you for your question! Our consultants will get back to you soon.</p>
                    </div>
                `;
                
                // Insert the new question at the top of the questions list
                document.querySelector('.card').insertBefore(newQuestion, document.querySelector('.question-form'));
                
                // Clear the textarea after successful submission
                document.getElementById('userQuestion').value = "";
            } else {
                alert("Error: " + response.message);
            }
        }
    };
    
    // Send the question data to the server
    xhr.send("question_text=" + encodeURIComponent(questionText));
}

// Optional: Toggle dropdown menu for user profile
document.getElementById("user-icon").addEventListener("click", function() {
    var dropdownMenu = document.getElementById("dropdown-menu");
    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
});

// Optional: Close the dropdown if the user clicks outside of it
document.addEventListener("click", function(event) {
    var dropdownMenu = document.getElementById("dropdown-menu");
    if (!dropdownMenu.contains(event.target) && event.target.id !== "user-icon") {
        dropdownMenu.style.display = "none";
    }
});
