Request.js
// Function to fetch pending requests and populate the request rows
function fetchPendingRequests() {
    fetch('fetch_pending_requests.php') // Ensure this path is correct
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const requestRows = document.getElementById('request-rows');
            requestRows.innerHTML = ''; // Clear existing rows

            data.forEach(request => {
                const newRow = document.createElement('div');
                newRow.className = 'request-row';
                newRow.innerHTML = 
                    <div>${request.date}</div>
                    <div>${request.request_id}</div>
                    <div>${request.employee_id}</div>
                    <div>${request.departmentdivision}</div>
                    <div>${request.guest_name} (${request.count})</div> <!-- Display guest name with count -->
                    <div>${request.company_place}</div>
                    <div>${request.serviceType}</div>
                    <div class="action-links">
                        <a href="#" class="approve-link" onclick="approveRequest('${request.request_id}'); return false;">Approve</a>
                        <a href="#" class="reject-link" onclick="rejectRequest('${request.request_id}'); return false;">Reject</a>
                    </div>
                ;
                requestRows.appendChild(newRow);
            });
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

// Function to approve a request
function approveRequest(requestId) {
    // Send a request to the server to approve the request
    fetch('approve_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ request_id: requestId, action: 'approve' })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log(Approved request ID: ${requestId});
            fetchPendingRequests(); // Refresh the list after approval
        } else {
            console.error('Error approving request:', data.error);
        }
    })
    .catch(error => {
        console.error('There was a problem with the approve operation:', error);
    });
}

// Function to reject a request
function rejectRequest(requestId) {
    // Send a request to the server to reject the request
    fetch('approve_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ request_id: requestId, action: 'reject' })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log(Rejected request ID: ${requestId});
            fetchPendingRequests(); // Refresh the list after rejection
        } else {
            console.error('Error rejecting request:', data.error);
        }
    })
    .catch(error => {
        console.error('There was a problem with the reject operation:', error);
    });
}

// Call the function to fetch pending requests when the page loads
document.addEventListener('DOMContentLoaded', fetchPendingRequests);
