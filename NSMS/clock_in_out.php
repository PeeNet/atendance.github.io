<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to login first!!.'); window.location.href='reset_password.html';</script>";
   
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle the clock-in and clock-out actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'clock_in') {
        // Clock-in logic
        $stmt = $conn->prepare("INSERT INTO shifts (employee_id, shift_start, status) VALUES (?, NOW(), 'Present')");
        if ($stmt->execute([$user_id])) {
            header("Location: workflow.php");
            exit;
        } else {
            echo "Error: Could not clock in.";
        }
    } elseif ($action == 'clock_out') {
        // Clock-out logic
        $stmt = $conn->prepare("UPDATE shifts SET shift_end = NOW() WHERE employee_id = ? AND shift_end IS NULL");
        if ($stmt->execute([$user_id])) {
            header("Location: workflow.php");
            exit;
        } else {
            echo "Error: Could not clock out.";
        }
    }
}
?>
