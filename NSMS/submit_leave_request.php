<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to login first!'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $leaveType = $_POST['leave-type'];
    $reason = $_POST['reason'];
    $startDate = $_POST['start-date'];
    $endDate = $_POST['end-date'];

    // Validate date order
    if (strtotime($startDate) > strtotime($endDate)) {
        echo "Start date cannot be after end date.";
        exit;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO leave_requests (employee_id, leave_type, reason, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $leaveType, $reason, $startDate, $endDate]);

    // Check if the insertion was successful
    if ($stmt->rowCount() > 0) {
        echo "Leave request submitted successfully.";
    } else {
        echo "Error submitting leave request.";
    }
}
?>
