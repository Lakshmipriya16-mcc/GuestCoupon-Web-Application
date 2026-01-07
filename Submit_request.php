<?php
session_start(); // Start the session

// Database configuration
$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "toyota"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}

// Assuming the employee_id is stored in session
$employee_id = $_SESSION['employee_id'] ?? null; // Adjust as per how you store employee_id in session

// Check if employee_id was fetched from session
if (!$employee_id) {
    die(json_encode(["success" => false, "error" => "Error: Employee ID not found in session."]));
}

// Function to generate a unique numeric request ID
function generateRequestId($length = 6) {
    return str_pad(rand(0, 999999), $length, '0', STR_PAD_LEFT); // Unique numeric ID
}

// Retrieve form data
$date = $_POST['date'] ?? '';
$serviceType = $_POST['serviceType'] ?? '';
$canteenType = $_POST['canteenType'] ?? '';
$guest_names = $_POST['guest_name'] ?? []; // Array of guest names
$company_places = $_POST['company_place'] ?? []; // Array of company names
$emails = $_POST['email'] ?? []; // Array of emails

// Initialize response array
$response = ["success" => false];

// Ensure all arrays have the same length
if (count($guest_names) === count($company_places) && count($guest_names) === count($emails)) {
    // Generate a single request ID to be used for all rows
    $request_id = generateRequestId();

    // Prepare the SQL statement to include employee_id
    $stmt = $conn->prepare("INSERT INTO guest_coupon_requests (request_id, date, serviceType, canteenType, guest_name, company_place, email, employee_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Loop through each row of guest data and insert it into the database
    $insertedCount = 0; // Track successful inserts
    for ($i = 0; $i < count($guest_names); $i++) {
        $guest_name = $guest_names[$i];
        $company_place = $company_places[$i];
        $email = $emails[$i];

        // Optional: Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['errors'][] = "Invalid email format for guest $guest_name.";
            continue; // Skip this iteration
        }

        // Bind the parameters for the current row, including employee_id
        $stmt->bind_param("ssssssss", $request_id, $date, $serviceType, $canteenType, $guest_name, $company_place, $email, $employee_id);

        // Execute the statement for each row
        if ($stmt->execute()) {
            $insertedCount++;
        } else {
            // Collect errors in response
            $response['errors'][] = "Error inserting row for guest $guest_name: " . $stmt->error;
        }
    }

    // On successful insertion, set success message and request ID
    if ($insertedCount > 0) {
        $response['success'] = true;
        $response['request_id'] = $request_id;
    } else {
        $response['error'] = "Error: No records were inserted.";
    }
} else {
    $response['error'] = "Error: Mismatched data. Please ensure all fields (guest name, company, email) are filled out for each guest.";
}

// Set the content type to application/json
header('Content-Type: application/json');

// Echo the JSON encoded response
echo json_encode($response);

// Close the statement and connection
$stmt->close();
$conn->close();
?>



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














