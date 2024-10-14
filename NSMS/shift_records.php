<?php
session_start();
require 'main.php'; // Database connection

// Fetch all shifts with employee names
$stmt = $conn->prepare("
    SELECT s.*, e.name AS employee_name 
    FROM shifts s 
    JOIN employees e ON s.employee_id = e.id
    ORDER BY s.shift_start DESC
");
$stmt->execute();
$shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Records</title>
    <link rel="stylesheet" href="admin.css"> <!-- Your CSS file -->
</head>
<body>
    <div class="admin-container">
        <h2>All Shift Records</h2>
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Shift Start</th>
                    <th>Shift End</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shifts as $shift): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($shift['employee_name']); ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($shift['shift_start'])); ?></td>
                        <td><?php echo $shift['shift_end'] ? date('Y-m-d H:i:s', strtotime($shift['shift_end'])) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($shift['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
