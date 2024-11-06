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

function submitReply(questionId) {
    var replyText = document.getElementById('reply' + questionId).value;

    if (replyText === "") {
        alert("Please enter a response before submitting.");
        return;
    }

    // Initialize the AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "submit_reply.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    // Callback function to handle the response
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            // If submission is successful, add the new reply to the page
            if (response.success) {
                var replyDiv = document.createElement('div');
                replyDiv.className = 'reply';
                replyDiv.innerHTML = `
                    <h4>Reply by Consultant:</h4>
                    <p>${response.reply_text}</p>
                `;

                // Append the reply below the respective question
                var replyContainer = document.querySelector('.question:has(#reply' + questionId + ') .reply-container');
                replyContainer.appendChild(replyDiv);
                
                // Remove the reply form after successful submission
                document.querySelector('.question:has(#reply' + questionId + ') .reply-form').style.display = 'none';
            } else {
                alert("Error: " + response.error);
            }
        }
    };

    // Send the reply data to the server
    xhr.send("reply_text=" + encodeURIComponent(replyText) + "&question_id=" + questionId);
}
