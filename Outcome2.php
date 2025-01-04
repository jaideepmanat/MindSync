<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Redirect to login if not logged in
    exit();
}

// Optional: Further restrict access based on user type
if ($_SESSION['user_type'] !== 'normal') {
    header("Location: login.html"); // Redirect if user type doesn't match
    exit();
}

// Assuming risk level is passed via session from the previous PHP page
$risk_level = $_SESSION['risk_level'] ?? null; // Default to null if not set

// Define consultant area filter based on risk level
$consultant_area_filter = [];
if ($risk_level === "High") {
    $consultant_area_filter = ["severe_mental_health_conditions", "both"];
} elseif ($risk_level === "Mid") {
    $consultant_area_filter = ["wellness_and_preventative_mental_health", "both"];
}

// Database connection
$host = "localhost";
$user = "root";
$password = ""; // XAMPP default
$dbname = "mindsync"; // Your database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL query to select consultants based on the filter
if (!empty($consultant_area_filter)) {
    $filter_values = "'" . implode("','", $consultant_area_filter) . "'";
    $sql = "SELECT id, name, email FROM users WHERE user_type = 'consultant' AND consultant_area IN ($filter_values)";
} else {
    $sql = "SELECT id, name, email FROM users WHERE user_type = 'consultant'";
}
$result = $conn->query($sql);

// Query to check which consultants are already booked by the logged-in user
$userId = $_SESSION['user_id'];
$bookedConsultantsSql = "SELECT consultant_id FROM booked_users WHERE user_id = '$userId'";
$bookedConsultantsResult = $conn->query($bookedConsultantsSql);
$bookedConsultants = [];

// Collect booked consultants
while ($row = $bookedConsultantsResult->fetch_assoc()) {
    $bookedConsultants[] = $row['consultant_id'];
}

// Query to get the ratings for consultants
$ratingsSql = "SELECT consultant_id, AVG(rating) as avg_rating FROM ratings GROUP BY consultant_id";
$ratingsResult = $conn->query($ratingsSql);
$ratings = [];

// Collect ratings
while ($row = $ratingsResult->fetch_assoc()) {
    $ratings[$row['consultant_id']] = $row['avg_rating'];
}

$conn->close(); 
if (isset($_SESSION['risk_level'])) {
    $risk_level = ucfirst(strtolower($_SESSION['risk_level'])); // Format risk level
    $risk_message = "<h1 style='color: #4CAF50; font-size: 1.8em; font-weight: bold; text-align: center; margin: 20px 0;'>
   Risk level : <strong>{$risk_level}</strong>
    </h1>";
} else {
    $risk_message = "";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultants</title>
    <link rel="stylesheet" href="Outcome2.css">
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
                <a href="home1.php">Home</a>
                <a href="Profile.php">View Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <br><br><br>

    <?php echo $risk_message; ?>

    <!-- Cards Section -->
    <section class="cards">
        <?php
        if ($result->num_rows > 0) {
            // Output data of each consultant
            while ($row = $result->fetch_assoc()) {
                $consultantId = $row['id'];
                $isBooked = in_array($consultantId, $bookedConsultants); // Check if the consultant is booked by the user
                $avgRating = isset($ratings[$consultantId]) ? $ratings[$consultantId] : 0; // Get average rating or 0 if not rated

                echo '<div class="card">';
                echo '<p>' . htmlspecialchars($row['name']) . '</p>';
                echo '<div class="rate">';
                // Display stars based on average rating
                echo '<div class="stars" id="avg-rating-' . strtolower(str_replace(' ', '-', $row['name'])) . '">';
                for ($i = 1; $i <= 5; $i++) {
                    echo $i <= $avgRating ? '★' : '☆';
                }
                echo '</div>';
                echo '<button class="rate-button" onclick="rateConsultant(\'' . addslashes($row['name']) . '\', ' . $row['id'] . ')">Rate</button>';
                echo '</div>';
                // Disable the booking button if already booked
                echo '<button class="book-button" onclick="bookConsultant(\'' . addslashes($row['name']) . '\')" ' . ($isBooked ? 'disabled' : '') . '>' . ($isBooked ? 'Booked' : 'Book Now') . '</button>';

                // Add "Send Message" button if booked
                if ($isBooked) {
                    echo '<button class="message-button" onclick="toggleMessageForm(' . $consultantId . ')">Send Message</button>';
                    echo '<div class="message-form" id="message-form-' . $consultantId . '" style="display: none;">';
                    echo '<div class="message-input-row">';
                    echo '<input type="text" placeholder="Type your message here" id="message-input-' . $consultantId . '">';
                    echo '<button class="message-button-submit" onclick="submitMessage(' . $consultantId . ')">Submit</button>';
                    echo '<button class="message-button-cancel" onclick="cancelMessage(' . $consultantId . ')">Cancel</button>';
                    echo '</div>';
                    echo '</div>';
                }

                echo '</div>';
            }
        } else {
            echo '<p>No consultants available.</p>';
        }
        ?>

        <div id="rate-popup" style="display:none;">
            <h3>Rate <span id="consultant-name"></span></h3>
            <div class="rating">
                <input type="radio" id="star5" name="rating" value="5" />
                <label for="star5">&#9733;</label>
                <input type="radio" id="star4" name="rating" value="4" />
                <label for="star4">&#9733;</label>
                <input type="radio" id="star3" name="rating" value="3" />
                <label for="star3">&#9733;</label>
                <input type="radio" id="star2" name="rating" value="2" />
                <label for="star2">&#9733;</label>
                <input type="radio" id="star1" name="rating" value="1" />
                <label for="star1">&#9733;</label>
            </div>
            <button class="popup-button" onclick="submitRating()">Submit</button>
            <button class="popup-button" onclick="closePopup()">Cancel</button>
        </div>
    </section>

    <script src="consult.js"></script>
</body>
</html>
