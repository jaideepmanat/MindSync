<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Optional: Further restrict access based on user type
if ($_SESSION['user_type'] !== 'normal' && basename(__FILE__) === 'home1.php') {
    header("Location: login.html"); // Redirect if user type doesn't match
    exit();
}

if ($_SESSION['user_type'] !== 'consultant' && basename(__FILE__) === 'home2.php') {
    header("Location: login.html"); // Redirect if user type doesn't match
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant View</title>
    <link rel="stylesheet" href="home2.css">
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
                <a href="table2.php">History</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <img src="image2.jpg" alt="Booked Users">
            <p>View the list of users who have booked a consultation with you.</p>
            <button onclick="showPopup()">View Users</button>
        </div>
        <div class="card">
            <img src="image1.jpg" alt="Community Page">
            <p>Engage with the mental health community and share insights.</p>
            <button onclick="location.href='CommunityConsult.php';">Go to Community</button>
        </div>
    </div>

   <!-- Popup -->
<div id="popup" class="popup">
    <div class="popup-content">
        <h3>Booked Users</h3>
        <table id="users-table">
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
            <!-- User rows will be populated here dynamically -->
        </table>
        <br>
        <button onclick="closePopup()">Close</button>
    </div>
</div>  


    <script src="home2.js"></script>
</body>
</html>
