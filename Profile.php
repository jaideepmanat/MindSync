<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$homePage = 'home1.php'; // Default
if (isset($_SESSION['user_type'])) {
    $homePage = ($_SESSION['user_type'] === 'consultant') ? 'home2.php' : 'home1.php';
}

require 'config.php';

// Fetch user details from the database
$stmt = $conn->prepare("SELECT name, email, mobile, user_type, consultant_area FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($name, $email, $mobile, $user_type, $consultant_area);
$stmt->fetch();
$stmt->close();

// Format consultant area if applicable
if ($user_type === 'consultant' && !empty($consultant_area)) {
    // Replace underscores with spaces and capitalize each word
    $formattedConsultantArea = ucwords(str_replace('_', ' ', $consultant_area));
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="Profile.css">
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
                <a href="<?php echo $homePage; ?>">Home</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Profile Card with Picture on Left -->
    <div class="card">
        <div class="profile-layout">
            <img src="https://cdn-icons-png.flaticon.com/512/147/147142.png" alt="User Icon" class="profile-icon">
            <div class="profile-details">
                <h2><?php echo htmlspecialchars($name); ?></h2>
                <div class="email-info">
                    <h5 class="credentials">Email:</h5>
                    <a class="mailid"><?php echo htmlspecialchars($email); ?></a>
                </div>
                <div class="Phn-no-info">
                    <h5 class="credentials">Phone Number:</h5>
                    <a class="Phn-no"><?php echo htmlspecialchars($mobile); ?></a>
                </div>
                <div class="user-type-info">
                    <h5 class="credentials">User Type:</h5>
                    <a class="user-type"><?php echo htmlspecialchars(ucwords($user_type)); ?></a>
                </div>
                <?php if ($user_type === 'consultant' && !empty($consultant_area)): ?>
                    <div class="consultant-area-info">
                        <h5 class="credentials">Area of Expertise:</h5>
                        <a class="consultant-area"><?php echo htmlspecialchars($formattedConsultantArea); ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="Profile.js"></script>
</body>
</html>
