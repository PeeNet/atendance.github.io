<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is an administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You do not have permission to view this page.";
    exit; 
}

// Fetch all employees
$stmt = $conn->prepare("SELECT id, name FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch shift counts and leave counts for each employee
$employeeShiftCounts = [];
$employeeLeaveCounts = []; // Array to hold leave counts
$employeeLeaveRequests = []; // Array to hold leave requests
foreach ($employees as $employee) {
    // Count shifts
    $stmt = $conn->prepare("SELECT COUNT(*) as shift_count FROM shifts WHERE employee_id = ?");
    $stmt->execute([$employee['id']]);
    $employeeShiftCounts[$employee['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['shift_count'];

    // Count leaves
    $stmt = $conn->prepare("SELECT COUNT(*) as leave_count FROM leave_requests WHERE employee_id = ?");
    $stmt->execute([$employee['id']]);
    $employeeLeaveCounts[$employee['id']] = $stmt->fetch(PDO::FETCH_ASSOC)['leave_count'];

    // Fetch leave requests for the employee
    $stmt = $conn->prepare("SELECT * FROM leave_requests WHERE employee_id = ?");
    $stmt->execute([$employee['id']]);
    $employeeLeaveRequests[$employee['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$unitFilter = ''; // Default is no filter
if (isset($_GET['unit']) && !empty($_GET['unit'])) {
    $unitFilter = $_GET['unit'];
}

// Fetch employees, possibly filtering by unit
if ($unitFilter) {
    $stmt = $conn->prepare("SELECT id, name, unit FROM employees WHERE unit = ?");
    $stmt->execute([$unitFilter]);
} else {
    $stmt = $conn->prepare("SELECT id, name, unit FROM employees");
    $stmt->execute();
}
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of employees
$totalEmployees = count($employees);

// Get total number of shifts
$stmt = $conn->prepare("SELECT COUNT(*) as total_shifts FROM shifts");
$stmt->execute();
$totalShifts = $stmt->fetch(PDO::FETCH_ASSOC)['total_shifts'];

// Get total number of leave requests
$stmt = $conn->prepare("SELECT COUNT(*) as total_leaves FROM leave_requests");
$stmt->execute();
$totalLeaves = $stmt->fetch(PDO::FETCH_ASSOC)['total_leaves'];

// Handle leave approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $leaveId = $_POST['leave_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE leave_requests SET approval_status = 'approved' WHERE id = ?");
        $stmt->execute([$leaveId]);
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE leave_requests SET approval_status = 'rejected' WHERE id = ?");
        $stmt->execute([$leaveId]);
    }

    // Redirect to avoid resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Employee Records</title>
    <link rel="stylesheet" href="admin.css"> <!-- Your CSS file -->
    <style>
        body {
            display: flex; /* Use flexbox for layout */
        }
        /* Admin container styling */
        .admin-container {
            padding: 20px;
            flex: 1; 
        }
        
        /* Left Navbar styling */
        .navbar {
            width: 200px; 
            height: 1000px;
            background-color: #3498db; 
            padding: 15px; 
            color: white; 
        }

        .navbar a {
            color: white; 
            text-decoration: none;
            display: block; 
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px; 
        }

        .navbar a:hover {
            background-color: #2980b9; /* Darker background on hover */
        }

        /* Title styling */
        .admin-title {
            text-align: center;
            font-size: 3em;
            color: #3498db; 
            margin-bottom: 20px; 
        }

        /* Summary container styling */
        .summary-container {
            background-color: #ffffff;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            gap: 20px; 
        }

       
    </style>
</head>
<body>

<div class="navbar">
 
    <a href="workflow.php">Home</a>
    <a href="employee_records.php">Employee Records</a>
    <a href="shift_records.php">Shift Records</a>
    <a href="leave-r.php">Leave Requests</a>
    <a href="settings.php">Settings</a>
    <a href="login.html">Logout</a>
</div>

<div class="admin-container">
   
    <h1 class="admin-title">LEYAATA HOSPITAL</h1>

    <!-- Summary Container -->
    <div class="summary-container">
        <h3>Summary</h3>
        <div class="summary-item">
            <p>Total Employees: <span><?php echo $totalEmployees; ?></span></p>
        </div>
        <a href="shift_records.php" style="text-decoration: none; color: inherit;">
            <div class="summary-item">
                <p>Total Shifts: 
                    <span><?php echo $totalShifts; ?></span> 
                </p>
            </div>
        </a>
        <a href="leave-r.php">
        <div class="summary-item">
            <p>Total Leave Requests: <span><?php echo $totalLeaves; ?></span></p>
        </div></a>
    </div>

    <form method="GET" action="" class="filter-form">
        <label for="unit" class="filter-label">Filter by Unit:</label>
        <select name="unit" id="unit" class="filter-select">
            <option value="">All Units</option>
            <option value="ICU">ICU</option>
            <option value="ER">ER</option>
            <option value="Maternity">Maternity</option>
            <option value="Emergency">Emergency</option>
            <option value="Surgery">Surgery</option>
            <option value="Pediatrics">Pediatrics</option>
            <!-- Add more unit options as needed -->
        </select>
        <button type="submit" class="filter-button">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Unit</th>
                <th>Total Shift Records</th>
                <th>Total Leave Records</th>
                <th>Action</th>
                <th>Leave Requests</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                    <td><?php echo htmlspecialchars($employee['unit']); ?></td>
                    <td><?php echo $employeeShiftCounts[$employee['id']]; ?></td>
                    <td><?php echo $employeeLeaveCounts[$employee['id']]; ?></td>
                    <td>
                        <form action="view_employee_records.php" method="POST">
                            <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                            <button type="submit">View Records</button>
                        </form>
                    </td>
                    <td>
                        <?php if (!empty($employeeLeaveRequests[$employee['id']])): ?>
                            <?php foreach ($employeeLeaveRequests[$employee['id']] as $leaveRequest): ?>
                                <div>
                                    <p><?php echo htmlspecialchars($leaveRequest['reason']); ?> (Status: <?php echo htmlspecialchars($leaveRequest['approval_status']); ?>)</p>
                                    <form method="POST">
                                        <input type="hidden" name="leave_id" value="<?php echo $leaveRequest['id']; ?>">
                                        <?php if ($leaveRequest['approval_status'] === 'pending'): ?>
                                            <button type="submit" name="action" value="approve">Approve</button>
                                            <button type="submit" name="action" value="reject">Reject</button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No leave requests</p>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
