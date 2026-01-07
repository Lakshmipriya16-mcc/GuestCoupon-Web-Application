Logout.php
<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page with a message (optional)
header("Location: login.html?logout=success"); // Redirect to login page with logout status
exit();
?>