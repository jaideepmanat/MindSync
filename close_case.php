<?php
session_start(); // Start the session
include 'config.php'; // Include your database configuration file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied.");
}

// Get the user_id from the request
if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $consultantId = $_SESSION['user_id'];

    // Start transaction to handle multiple updates
    $conn->begin_transaction();

    try {
        // Prepare SQL statement to delete the booked user from the booked_users table
        $sql = "DELETE FROM booked_users WHERE user_id = ? AND consultant_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $consultantId);

        if ($stmt->execute()) {
            // Prepare SQL to update the history table and set status to 'Closed'
            $historyUpdateQuery = "UPDATE history SET status = 'Closed', closed_date = NOW() WHERE user_id = ? AND consultant_id = ? AND status = 'Active'";
            $stmt->close();
            $stmt = $conn->prepare($historyUpdateQuery);
            $stmt->bind_param("ii", $userId, $consultantId);

            if ($stmt->execute()) {
                // Commit the transaction if everything is successful
                $conn->commit();
                echo json_encode(["success" => true]);
            } else {
                // Rollback the transaction if history update fails
                $conn->rollback();
                echo json_encode(["success" => false, "error" => "Failed to update history: " . $stmt->error]);
            }
        } else {
            // Rollback if the booked user deletion fails
            $conn->rollback();
            echo json_encode(["success" => false, "error" => "Failed to delete booked user: " . $stmt->error]);
        }
        $stmt->close();
    } catch (Exception $e) {
        // Rollback if there’s any exception
        $conn->rollback();
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "User ID not provided."]);
}

$conn->close(); // Close the database connection
?>
