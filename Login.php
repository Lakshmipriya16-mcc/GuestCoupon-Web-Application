<?php
// Start session to track login state
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toyota";

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the employee ID and password from the form, sanitize input
    $employee_id = $conn->real_escape_string($_POST['employee_id']);
    $password = $_POST['password'];

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the employee ID exists in the database
    if ($result->num_rows > 0) {
        // Fetch the user's data
        $user = $result->fetch_assoc();

        // Verify the entered password with the hashed password from the database
        if (password_verify($password, $user['password'])) {
            // If the password is correct, regenerate session ID for security
            session_regenerate_id(true);
            $_SESSION['employee_id'] = $employee_id;

            // Redirect to the dashboard page
            header("Location: dashboard.html");
            exit();
        } else {
            // Redirect back to the login page with an error message
            header("Location: login.html?error=invalid_password");
            exit();
        }
    } else {
        // Redirect back to the login page with an error message
        header("Location: login.html?error=employee_id_not_found");
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>