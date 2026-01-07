Fetch_status.php
<?php
// fetch_status.php
$servername = "localhost"; // Your database server
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "toyota"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . $conn->connect_error]));
}

// Prepare SQL query to fetch status details
$sql = "SELECT date, request_id, canteenType, serviceType, guest_name FROM guest_coupon_requests WHERE status = 'approved'";
$result = $conn->query($sql);

$statusData = [];
if ($result) {
    if ($result->num_rows > 0) {
        // Fetch the data
        while ($row = $result->fetch_assoc()) {
            $statusData[] = $row;
        }
    }
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(["success" => true, "data" => $statusData]);
} else {
    // Handle query error
    echo json_encode(["success" => false, "error" => $conn->error]);
}

$conn->close();
?>