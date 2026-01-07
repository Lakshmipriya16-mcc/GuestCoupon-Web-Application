<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toyota";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to validate input
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and validate user inputs
    $employee_id = validate_input($_POST['employee_id']);
    $password = validate_input($_POST['password']);
    $name = validate_input($_POST['name']);
    $designation = validate_input($_POST['designation']);
    $departmentdivision = validate_input($_POST['departmentdivision']);
    $costcenterno = validate_input($_POST['costcenterno']);
    $email = validate_input($_POST['email']);
    $contactnumber = validate_input($_POST['contactnumber']);

    // Ensure required fields are not empty
    if (empty($employee_id) || empty($password) || empty($email) || empty($contactnumber)) {
        die("Employee ID, password, email, and contact number are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Validate contact number (ensure it's numeric and 10 digits)
    if (!preg_match('/^\d{10}$/', $contactnumber)) {
        die("Contact Number must be 10 digits.");	
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Initialize profilePhoto variable
    $profilePhoto = "uploads/default-photo.png"; // Default photo if no file is uploaded

    // Check if the file was uploaded without errors
    if (isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $profilePhotoFile = $_FILES['profilePhoto'];

        // Validate the file (you can customize the validations based on your needs)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']; // Allowed MIME types
        $fileType = mime_content_type($profilePhotoFile['tmp_name']);

        // Validate file size (e.g., max 2MB)
        if ($profilePhotoFile['size'] > 2 * 1024 * 1024) {
            die("File size must be less than 2MB.");
        }

        if (in_array($fileType, $allowedTypes)) {
            // Sanitize the file name and move it to your desired directory
            $fileName = uniqid() . "_" . basename($profilePhotoFile['name']); // Unique file name
            $targetDir = "uploads/"; // Ensure this directory exists
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($profilePhotoFile['tmp_name'], $targetFilePath)) {
                $profilePhoto = $targetFilePath; // Store the path to the file
            } else {
                die("Sorry, there was an error uploading your file.");
            }
        } else {
            die("Invalid file type. Please upload an image (JPEG, PNG, GIF).");
        }
    } else {
        echo "Using default photo.";
    }

    // First prepared statement for the users table
    $stmt = $conn->prepare("INSERT INTO users (employee_id, password, name, designation, departmentdivision, costcenterno, email, contactnumber, profilePhoto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $employee_id, $hashed_password, $name, $designation, $departmentdivision, $costcenterno, $email, $contactnumber, $profilePhoto);

    // Execute the statement for the users table
    if ($stmt->execute()) {
        // Prepare the second statement for the guest_coupon_requests table
        $stmt2 = $conn->prepare("INSERT INTO guest_coupon_requests (employee_id, departmentdivision) VALUES (?, ?)");
        $stmt2->bind_param("ss", $employee_id, $departmentdivision);

        // Execute the statement for the guest_coupon_requests table
        if ($stmt2->execute()) {
            // Redirect to login page after successful signup
            header("Location: login.html");
            exit; // Ensure no further code is executed
        } else {
            echo "Error inserting into guest_coupon_requests: " . $stmt2->error;
        }

        // Close the second statement
        $stmt2->close();
    } else {
        echo "Error inserting into users: " . $stmt->error;
    }

    // Close the first statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>