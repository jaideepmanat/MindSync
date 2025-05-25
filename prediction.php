<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Include database configuration
include 'config.php';

// Fetch predictions for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT created_at, prediction FROM predictions WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Predictions</title>
    <link rel="stylesheet" href="prediction.css">
    <link rel="icon" type="image/x-icon" href="icon.png">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="left">
            <h1>MindSync</h1>
        </div>
        <div class="right">
            <div class="mailbox" onclick="window.location.href='mail.php';">
                <span>&#9993;</span>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="Profile.php">View Profile</a>
                <a href="table.php">History</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Predictions Table -->
    <section class="predictions">
        <h2>Your Predictions</h2>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Prediction</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['prediction']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No predictions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <script src="prediction.js"></script>
</body>
</html>
