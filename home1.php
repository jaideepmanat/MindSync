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
    <title>MindSync</title>
    <link rel="stylesheet" href="home1.css">
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
                <a href="#">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Motivational Quotes -->
    <section class="quotes">
        <h2 id="quote"></h2>
    </section>

    <!-- Cards Section -->
    <section class="cards">
        <div class="card">
            <img src="image1.jpg" alt="Survey Image" class="card-image">
            <p>Take a short survey to help us understand your needs and preferences better.</p>
            <button>Take a Survey</button>
        </div>
        <div class="card">
            <img src="image2.jpg" alt="Consult Image" class="card-image">
            <p>Get connected with a certified expert who can offer personalized guidance.</p>
            <button onclick="location.href='consult.php';">Consult an Expert</button>
        </div>
        <div class="card">
            <img src="image3.jpg" alt="Community Image" class="card-image">
            <p>Join our community and share your experiences and support others.</p>
            <button onclick="location.href='CommunityUser.php';">Community</button>
        </div>
    </section>
    

    <script src="home1.js"></script>
</body>
</html>
