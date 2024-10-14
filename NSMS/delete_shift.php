<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to login first!!.'); window.location.href='reset_password.html';</script>";
   
    exit;
}

// Check if a shift ID was provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['shift_id'])) {
    $shift_id = $_POST['shift_id'];
    $user_id = $_SESSION['user_id'];

    // Delete the shift record
    $stmt = $conn->prepare("DELETE FROM shifts WHERE id = ? AND employee_id = ?");
    if ($stmt->execute([$shift_id, $user_id])) {
        echo "Shift deleted successfully.";
        header("Location: workflow.php");
        exit;
    } else {
        echo "Error: Could not delete shift.";
    }
}
?>
