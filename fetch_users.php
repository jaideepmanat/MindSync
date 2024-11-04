<?php
session_start(); // Start the session
include 'config.php'; // Include your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied.");
}

// Get the consultant ID from the session
$consultantId = $_SESSION['user_id']; // Assuming this is the ID of the logged-in consultant

// Prepare SQL statement to fetch booked users for the consultant
$sql = "SELECT u.id, u.name, u.mobile, u.email FROM booked_users b
        JOIN users u ON b.user_id = u.id
        WHERE b.consultant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $consultantId); // Assuming consultant_id is an integer
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Fetch user data
    }
} 

echo json_encode($users); // Return users as JSON
$stmt->close();
$conn->close(); // Close the database connection
?>
