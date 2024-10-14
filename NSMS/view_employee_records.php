<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You do not have permission to view this page.";
    exit;
}

// Get employee ID from the POST request
if (!isset($_POST['employee_id'])) {
    echo "No employee selected.";
    exit;
}

$employee_id = $_POST['employee_id'];

// Fetch employee records
$stmt = $conn->prepare("SELECT * FROM shifts WHERE employee_id = ? ORDER BY shift_start DESC");
$stmt->execute([$employee_id]);
$shift_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch employee name
$stmt = $conn->prepare("SELECT name FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($employee['name']); ?> - Records</title>
    <link rel="stylesheet" href="admin.css"> <!-- Your CSS file -->
</head>
<body>
    <div class="record-container">
        <h2>Records for <?php echo htmlspecialchars($employee['name']); ?></h2>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Shift Start</th>
                    <th>Shift End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shift_history as $shift): ?>
                    <tr>
                        <td><?php echo date('Y-m-d', strtotime($shift['shift_start'])); ?></td>
                        <td><?php echo date('H:i:s', strtotime($shift['shift_start'])); ?></td>
                        <td>
                            <?php 
                                echo $shift['shift_end'] ? date('H:i:s', strtotime($shift['shift_end'])) : 'N/A';
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($shift['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer Section -->
    <!-- <footer class="page-footer">
        <div class="footer-content">
            <p>Developed by <a href="ampomahpeter13@gmail.com" target="_blank">Ampomah Peter</a></p>
            <p>&copy; 2024 - All Rights Reserved</p>
        </div>
    </footer> -->
</body>
</html>
