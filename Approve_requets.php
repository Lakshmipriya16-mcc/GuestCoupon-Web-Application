Approve_requets.php
<?php
// Database connection parameters
$host = 'localhost';
$db = 'toyota';
$user = 'root';
$pass = '';

// Create a new database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Get the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if the required data is present
if (isset($data['request_id']) && isset($data['action'])) {
    $request_id = $data['request_id'];
    $action = $data['action'];

    // Prepare the SQL query based on the action
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE guest_coupon_requests SET status='approved' WHERE request_id=?");
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE guest_coupon_requests SET status='rejected' WHERE request_id=?");
    } else {
        echo json_encode(["success" => false, "error" => "Invalid action."]);
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param("s", $request_id);

    // Execute the query and return the response
    if ($stmt->execute() === TRUE) {
        echo json_encode(["success" => true, "updated_status" => $action === 'approve' ? 'approved' : 'rejected']);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid input."]);
}

// Close the database connection
$conn->close();
?>