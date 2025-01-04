<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// If additional code or display logic exists for the outcome, it goes below this comment
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CommunityConsultant</title>
    <link rel="stylesheet" href="Outcome.css">
</head>
<body>
    <!-- Navbar -->
    <nav>
        <div class="left">
            <h1>MindSync</h1>
        </div>
        <div class="right">
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="user-icon" id="user-icon">
            <div class="dropdown" id="dropdown-menu">
                <a href="Profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="card">
        <h2>Congratulations!</h2>
        <p>You are in great mental health. Keep it up!</p>
        <button class="button" onclick="location.href='home1.php';">Go Home</button>
    </div>
    <script src="Outcome.js"></script>
</body>
</html>
