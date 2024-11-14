<?php
// Include the database connection
include 'config.php';

// Start the session to access the consultant ID
session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if necessary parameters are provided
    if (isset($_POST['user_id'], $_POST['feedback_text']) && !empty($_POST['user_id']) && !empty($_POST['feedback_text'])) {
        // Retrieve data from the POST request
        $userId = $_POST['user_id'];
        $consultantId = $_SESSION['user_id']; // Assuming consultant ID is stored in the session
        $feedbackText = $_POST['feedback_text'];

        // Prepare the SQL query to insert feedback
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, consultant_id, feedback_text, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $userId, $consultantId, $feedbackText);

        // Execute the query and check for success
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            // If an error occurs, return the error message
            echo json_encode(['success' => false, 'error' => 'Failed to submit feedback']);
        }

        // Close the prepared statement and database connection
        $stmt->close();
        $conn->close();
    } else {
        // Missing parameters
        echo json_encode(['success' => false, 'error' => 'Missing user_id or feedback_text']);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
