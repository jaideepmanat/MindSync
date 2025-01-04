<?php
include 'config.php'; // Database connection

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['action'])) {
    $id = intval($data['id']);
    $action = $data['action'] === 'accept' ? 'Accepted' : 'Rejected';

    // Update the status in consultant_pending table
    $stmt = $conn->prepare("UPDATE consultant_pending SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $id);

    if ($stmt->execute() && $action === 'Accepted') {
        // Fetch the accepted record from consultant_pending
        $fetchStmt = $conn->prepare("SELECT name, email, mobile, user_type, consultant_area, password, created_at FROM consultant_pending WHERE id = ?");
        $fetchStmt->bind_param("i", $id);
        $fetchStmt->execute();
        $result = $fetchStmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Insert the data into the users table, excluding the `id` column
            $insertStmt = $conn->prepare(
                "INSERT INTO users (name, email, mobile, user_type, consultant_area, password, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $insertStmt->bind_param(
                "sssssss",
                $row['name'],
                $row['email'],
                $row['mobile'],
                $row['user_type'],
                $row['consultant_area'],
                $row['password'],
                $row['created_at']
            );

            if ($insertStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'User accepted and added to users table.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add user to users table.']);
            }

            $insertStmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'No record found to insert into users table.']);
        }

        $fetchStmt->close();
    } elseif ($action === 'Rejected') {
        echo json_encode(['success' => true, 'message' => 'User rejected.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
