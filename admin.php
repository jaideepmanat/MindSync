<?php
// Start the session
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    // Redirect to the login page if the user is not authorized
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="left">
            <h1>MindSync</h1>
        </div>
        <div class="right">
            <div class="admin">
                <span>Admin</span>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Username (Email)</th>
                    <th>Date & Time Registered</th>
                    <th>Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php
    // Include the database configuration file
    include 'config.php';

    // Fetch data from the consultant_pending table
    $query = "SELECT id, email, created_at, file_path, status FROM consultant_pending";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Loop through and display rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr data-id='" . $row['id'] . "'>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "<td class='popup-trigger' data-image='" . htmlspecialchars($row['file_path']) . "'>&#128203;</td>";
            
            if ($row['status'] === 'Accepted') {
                echo "<td><span class='status accepted'>Accepted</span></td>";
            } elseif ($row['status'] === 'Rejected') {
                echo "<td><span class='status rejected'>Rejected</span></td>";
            } else {
                echo "<td><button class='tick'>Accept</button> <button class='cross'>Reject</button></td>";
            }
            
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No pending consultants.</td></tr>";
    }
    ?>
</tbody>

        </table>
        <div class="overlay"></div>
        <div class="popup-box">
            <button class="close-btn">&times;</button>
            <div class="popup-content"></div>
        </div>
    </div>
    <script src="admin.js"></script>
</body>
</html>
