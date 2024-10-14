<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in first.";
    exit;
}

// Fetch the user's role
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM employees WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user is an admin
if ($user && $user['role'] === 'admin') {
    // Redirect to the admin panel
    header('Location: admin.php');
    exit();
} else {
    // If not an admin, show an alert message
    echo "<script>alert('Access denied. You do not have permission to view this page.'); window.location.href='workflow.php';</script>";
    exit();
}

?>

