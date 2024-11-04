<?php
session_start(); // Start the session
session_destroy(); // Destroy all session data
header("Location: login.html"); // Redirect to login page
exit();
?>
