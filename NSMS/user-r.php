<?php
session_start();
require 'main.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Access denied. You must be logged in to view this page.";
    exit;
}

// Get the logged-in user's ID
$loggedInUserId = $_SESSION['user_id'];

// Fetch leave requests for the logged-in user
$stmt = $conn->prepare("
    SELECT lr.*, e.name AS employee_name
    FROM leave_requests lr
    JOIN employees e ON lr.employee_id = e.id
    WHERE lr.employee_id = ?
    ORDER BY lr.created_at DESC
");
$stmt->execute([$loggedInUserId]);
$leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count the total leave requests for the logged-in user
$totalLeaveRequests = count($leaveRequests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Leave Requests</title>
    <link rel="stylesheet" href="style.css"> <!-- Your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .leave-requests-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #3498db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .summary {
            margin-top: 20px;
            font-size: 1.2em;
            color: #2980b9;
        }
    </style>
</head>
<body>
<div class="leave-requests-container">
    <h2>Your Leave Requests</h2>

    <div class="summary">
        Total Leave Requests: <strong><?php echo $totalLeaveRequests; ?></strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Reason</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Submitted On</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($totalLeaveRequests > 0): ?>
    <?php foreach ($leaveRequests as $request): ?>
        <tr>
            <td><?php echo htmlspecialchars($request['employee_name']); ?></td>
            <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
            <td><?php echo htmlspecialchars($request['reason']); ?></td>
            <td><?php echo htmlspecialchars(date("Y-m-d h:i A", strtotime($request['start_date']))); ?></td>
            <td><?php echo htmlspecialchars(date("Y-m-d h:i A", strtotime($request['end_date']))); ?></td>
            <td><?php echo htmlspecialchars($request['approval_status']); ?></td>
            <td><?php echo htmlspecialchars(date("Y-m-d h:i A", strtotime($request['created_at']))); ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7">No leave requests found.</td> <!-- This will display when there are no leave requests -->
    </tr>
<?php endif; ?> 

              
        </tbody>
    </table>
</div>
</body>
</html>
