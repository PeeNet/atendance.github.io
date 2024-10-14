<?php
session_start();
require 'main.php'; // Include your database connection file

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You do not have permission to view this page.";
    exit;
}

// Fetch all leave requests with employee details
$stmt = $conn->prepare("
    SELECT lr.*, e.name AS employee_name
    FROM leave_requests lr
    JOIN employees e ON lr.employee_id = e.id
    ORDER BY lr.created_at DESC
");
$stmt->execute();
$leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count the total leave requests
$totalLeaveRequests = count($leaveRequests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Leave Requests</title>
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
    <h2>All Leave Requests</h2>

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
        <?php foreach ($leaveRequests as $request): ?>
    <tr>
        <td><?php echo htmlspecialchars($request['employee_name']); ?></td>
        <td><?php echo htmlspecialchars($request['leave_type']); ?></td>
        <td><?php echo htmlspecialchars($request['reason']); ?></td>
        
        <!-- Format the start_date and end_date to include AM/PM -->
        <td><?php echo htmlspecialchars(date("Y-m-d h:i A", strtotime($request['start_date']))); ?></td>
        <td><?php echo htmlspecialchars(date("Y-m-d h:i A", strtotime($request['end_date']))); ?></td>
        
        <td><?php echo htmlspecialchars($request['approval_status']); ?></td>
        <td><?php echo htmlspecialchars(date("Y-m-d h:i A", strtotime($request['created_at']))); ?></td>
    </tr>
<?php endforeach; ?>

        </tbody>
    </table>
</div>
</body>
</html>
