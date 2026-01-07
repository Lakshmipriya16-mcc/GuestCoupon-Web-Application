Fetch_pending_requests.php
<?php
// Database connection parameters
$host = 'localhost'; // Change if your database host is different
$db = 'toyota'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password (if applicable)

// Create a new database connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch pending requests with additional details using JOIN
$sql = "SELECT 
            g.request_id, 
            g.date, 
            g.employee_id, 
            u.departmentdivision, 
            g.guest_name, 
            g.company_place, 
            g.serviceType,
            COUNT(g.guest_name) AS count  -- Count the number of guests
        FROM 
            guest_coupon_requests g 
        JOIN 
            users u ON g.employee_id = u.employee_id 
        WHERE 
            g.status = 'pending'
        GROUP BY 
            g.request_id, g.employee_id";  // Group by request_id and employee_id

$result = $conn->query($sql);

// Initialize an array to hold the requests
$requests = [];

// Check if there are results and fetch them into the array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row; // Add each request to the array
    }
}

// Return the results as a JSON response
header('Content-Type: application/json');
echo json_encode($requests);

// Close the database connection
$conn->close();
?>