<?php
session_start(); // Start or continue the session

// Check if employee ID is stored in the session (i.e., if the user is logged in)
if (!isset($_SESSION['employee_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.html");
    exit();
}

// Database connection details
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "toyota"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error); // Log the error
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in user's employee ID
$employee_id = $_SESSION['employee_id'];

// Prepare the SQL query to fetch user details
$sql = "SELECT employee_id, profilePhoto, name, designation, departmentDivision, costCenterNo, email FROM users WHERE employee_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("SQL preparation failed: " . $conn->error); // Log the error
    die("SQL preparation failed: " . $conn->error);
}

// Bind the parameter and execute the statement
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

// Default values
$profilePhoto = "default-profile.png"; // Default image
$name = "Guest"; // Default name
$designation = "Not specified"; // Default designation
$departmentDivision = "Not specified"; // Default department division
$costCenterNo = "Not specified"; // Default cost center number
$email = "Not specified"; // Default email

// Check if any rows were returned
if ($row = $result->fetch_assoc()) {
    // Fetch user details
    $profilePhoto = !empty($row['profilePhoto']) ? $row['profilePhoto'] : $profilePhoto;
    $name = !empty($row['name']) ? $row['name'] : $name;
    $designation = !empty($row['designation']) ? $row['designation'] : $designation;
    $departmentDivision = !empty($row['departmentDivision']) ? $row['departmentDivision'] : $departmentDivision;
    
    $costCenterNo = !empty($row['costCenterNo']) ? $row['costCenterNo'] : $costCenterNo;
    $email = !empty($row['email']) ? $row['email'] : $email;
    $employee_id = $row['employee_id']; // Update the employee ID
} else {
    error_log("No user found with employee ID: " . $employee_id); // Log if no user found
}

// Clean up
$stmt->close();
$conn->close();

// Prepare response data
$response = array(
    "profilePhoto" => $profilePhoto,
    "name" => $name,
    "designation" => $designation,
    "departmentDivision" => $departmentDivision,
   
    "costCenterNo" => $costCenterNo,
    "email" => $email,
    "employee_id" => $employee_id // Include employee ID in the JSON response
);

// Set content type to application/json
header('Content-Type: application/json');

// Log the response for debugging
error_log("Dashboard response: " . json_encode($response));

// Output the JSON response
echo json_encode($response);
?>









