<?php
session_start(); // Start the session
include 'config.php'; // Include your database configuration file

// Check if the user is logged in as consultant
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'consultant') {
    header("HTTP/1.1 403 Forbidden");
    exit("Access denied.");
}

// Get the logged-in consultant's user_id
$consultant_id = $_SESSION['user_id'];

// Query to fetch history for the consultant, showing user names
$sql = "
    SELECT h.date_registered, h.status, h.closed_date, u.name AS user_name
    FROM history h
    JOIN users u ON h.user_id = u.id
    WHERE h.consultant_id = ? 
    ORDER BY h.date_registered DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $consultant_id); // Bind consultant_id to the query
$stmt->execute();
$result = $stmt->get_result();

// Fetch results
$history_entries = [];
while ($row = $result->fetch_assoc()) {
    $history_entries[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Status Table</title>
    <link rel="stylesheet" href="table.css">
</head>
<body>
     <!-- Navbar -->
     <nav>
        <div class="left">
            <h1>MindSync</h1>
        </div>
        <div class="right">
            <div class="mailbox">
                <span>&#9993;</span>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="home2.php">Home</a>
                <a href="Profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date Registered On</th>
                    <th>User Name</th>
                    <th>Status</th>
                    <th>Closed Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($history_entries) > 0): ?>
                    <?php foreach ($history_entries as $entry): ?>
                        <tr>
                            <!-- Format date_registered to show both date and time -->
                            <td><?= date('d-m-Y H:i', strtotime($entry['date_registered'])) ?></td>
                            <td><?= htmlspecialchars($entry['user_name']) ?></td>
                            <td style="color: <?= $entry['status'] === 'Active' ? '#00ff00' : '#ed4343' ?>"><?= $entry['status'] ?></td>
                            <td><?= $entry['closed_date'] ? date('d-m-Y H:i', strtotime($entry['closed_date'])) : '--' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No history available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="table.js"></script>
</body>
</html>
