<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit();
}

// Include the database connection file
require 'config.php'; 

// Check if the request method is POST and the required parameters are present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_text']) && isset($_POST['question_id'])) {
    // Get the reply text and question ID
    $replyText = $conn->real_escape_string(trim($_POST['reply_text']));
    $questionId = intval($_POST['question_id']); // Ensure question ID is an integer
    $consultantId = $_SESSION['user_id']; // Get the consultant's ID from session

    // Check for empty reply text
    if (empty($replyText)) {
        echo json_encode(['success' => false, 'error' => 'Reply text cannot be empty.']);
        exit();
    }

    // Insert the reply into the database
    $sql = "INSERT INTO replies (question_id, consultant_id, reply_text, created_at) VALUES ('$questionId', '$consultantId', '$replyText', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'reply_text' => $replyText]);
    } else {
        // Log the SQL error message for debugging
        error_log("Database error: " . $conn->error); // Log the error
        echo json_encode(['success' => false, 'error' => 'Failed to post reply: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}

// Close the database connection
$conn->close();
?>
