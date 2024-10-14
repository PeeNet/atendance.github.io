<?php
session_start();
require 'main.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You need to login first!!.'); window.location.href='reset_password.html';</script>";
   
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current shift (if any)
$stmt = $conn->prepare("SELECT * FROM shifts WHERE employee_id = ? AND shift_end IS NULL");
$stmt->execute([$user_id]);
$current_shift = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch shift history
$stmt = $conn->prepare("SELECT * FROM shifts WHERE employee_id = ? ORDER BY shift_start DESC");
$stmt->execute([$user_id]);
$shift_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Fetch the user's details
// $stmt = $conn->prepare("SELECT name, role FROM employees WHERE id = ?");
// $stmt->execute([$user_id]);
// $user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch the user's details, including the unit
$stmt = $conn->prepare("SELECT name, role, unit FROM employees WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data is retrieved successfully
if (!$user) {
    header('location: login.html');
    exit;
}


// Check if user data is retrieved successfully
if (!$user) {
   header('location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOME PAGE</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your CSS file -->
</head>
<style>
    .delete-button {
    background-color: #e74c3c; /* Red background */
    color: white; /* White text */
    padding: 5px 10px;
    font-size: 0.8em;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.delete-button:hover {
    background-color: #c0392b; /* Darker red on hover */
}

.delete-button:active {
    transform: scale(0.95); /* Slight shrink on click */
}

/* Position the admin access button in the bottom-left corner */
.admin-access {
    position: fixed; /* Fix the position on the page */
   /* Distance from the bottom */
    left: 20px; /* Distance from the left */
    z-index: 1000; /* Ensure it stays on top of other elements */
}

/* Make the button smaller */
.admin-button {
    background-color: #3498db; /* Blue background */
    color: white; /* White text */
    padding: 8px 15px; /* Smaller padding */
    font-size: 0.9em; /* Smaller font size */
    border: none; /* Remove default border */
    border-radius: 20px; /* Slightly rounded button */
    font-weight: bold; /* Bold text */
    cursor: pointer; /* Pointer on hover */
    transition: all 0.3s ease; /* Smooth transitions */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Subtle shadow */
}

/* Hover effect to make the button larger */
.admin-button:hover {
    background-color: #2980b9; /* Darker blue on hover */
    padding: 10px 20px; /* Slightly larger on hover */
    font-size: 1em; /* Slightly larger font on hover */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Stronger shadow on hover */
}

/* Active (clicked) state */
.admin-button:active {
    transform: scale(0.95); /* Slight shrink on click */
    box-shadow: none; /* Remove shadow on click */
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .admin-button {
        padding: 6px 12px; /* Even smaller for mobile */
        font-size: 0.8em; /* Smaller font for mobile */
    }
}

.logout-button {
    background-color: #e74c3c; /* Red background */
    color: white; /* White text */
    padding: 8px 15px;
    font-size: 0.9em;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Subtle shadow */

    position: fixed; /* Fixed positioning to keep it in the top right */
    top: 105px; /* Distance from the top */
    right: 20px; /* Distance from the right */
    z-index: 1000; /* Ensure it stays above other content */
}

.logout-button:hover {
    background-color: #c0392b; /* Darker red on hover */
}

.logout-button:active {
    transform: scale(0.95); /* Slight shrink on click */
}

.unit-t{
    color:red;
    font-size: 20px;
}
</style>
<body>
    <div class="user-workflow">
        
    <h2>Welcome, 
        <?php
            // Display the user's name safely
            echo htmlspecialchars($user['name']);
        ?>
    </h2>
    <h2  class=" unit-t">Your Unit: <?php echo htmlspecialchars($user['unit']); ?></h2> <!-- Added this line to display the unit -->


<!-- Logout Button -->
<!-- Logout Button -->
<form id="logoutForm" method="POST" action="logout.php">
    <button type="button" class="logout-button" onclick="confirmLogout()">Logout</button>
</form>


<script>
function confirmLogout() {
    if (confirm('Do you really want to log out?')) {
        // If user confirms, submit the form to log out
        document.getElementById('logoutForm').innerHTML += '<input type="hidden" name="confirm_logout" value="1">';
        document.getElementById('logoutForm').submit();
    } else {
        // If user cancels, stay on the same page (workflow)
        window.location.href = 'workflow.php';
    }
}
</script>

    <!-- Admin Access Button -->
<div class="admin-access">
    <form action="admin_access.php" method="POST">
        <button type="submit" name="admin_access" class="admin-button">Admin Access Only</button>
    </form>
</div>
<a href="user-r.php">
 <br>
 <br><div class="admin-access">
    <form >
        <button type="button" name="admin_access" class="admin-button">View Your Reguest</button>
    </form>
</div>
</a>



        <!-- Current Shift Status -->
        <div class="current-shift-status">
            <h3>Current Shift Status</h3>
            <p id="shift-status">
                <?php if ($current_shift): ?>
                    You are currently: <span>Clocked In</span><br>
                    Shift Started at: <?php echo $current_shift['shift_start']; ?>
                <?php else: ?>
                    You are currently: <span>Clocked Out</span>
                <?php endif; ?>
            </p>
        </div>

        <!-- Clock In/Out Buttons -->
        <div class="clock-buttons">
            <form action="clock_in_out.php" method="POST">
                <?php if ($current_shift): ?>
                    <button type="submit" name="action" value="clock_out">Clock Out</button>
                <?php else: ?>
                    <button type="submit" name="action" value="clock_in">Clock In</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Shift History -->
<div class="shift-history">
    <h3>Your Shift History</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Shift Start</th>
                <th>Shift End</th>
                <th>Status</th>
                <th>Action</th> <!-- Added this column for the delete button -->
            </tr>
        </thead>
        <tbody>
        <?php foreach ($shift_history as $shift): ?>
    <tr>
        <td><?php echo date('Y-m-d', strtotime($shift['shift_start'])); ?></td>
        <td><?php echo date('h:i A', strtotime($shift['shift_start'])); ?></td> <!-- Updated for AM/PM -->
        <td>
            <?php 
                echo $shift['shift_end'] ? date('h:i A', strtotime($shift['shift_end'])) : 'N/A'; // Updated for AM/PM
            ?>
        </td>
        <td><?php echo $shift['status']; ?></td>
        <td>
            <form action="delete_shift.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this shift?');">
                <input type="hidden" name="shift_id" value="<?php echo $shift['id']; ?>">
                <button type="submit" class="delete-button">Delete</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>

        </tbody>
    </table>
</div>


<div class="leave-requests">
    <h3>Request Leave</h3>
    <form id="leave-form" action="submit_leave_request.php" method="POST">
        <div>
            <label for="leave-type">Leave Type:</label>
            <select name="leave-type" required>
                <option value="" disabled selected>Select Leave Type</option>
                <option value="Annual Leave">Annual Leave</option>
                <option value="Sick Leave">Sick Leave</option>
                <option value="Casual Leave">Casual Leave</option>
                <option value="Maternity Leave">Maternity Leave</option>
                <option value="Paternity Leave">Paternity Leave</option>
                <option value="Unpaid Leave">Unpaid Leave</option>
            </select>
        </div>
        
        <div>
            <label for="reason">Reason for Leave:</label>
            <textarea name="reason" placeholder="Please provide your reason for leave" required></textarea>
        </div>

        <div>
            <label for="start-date">Start Date:</label>
            <input type="date" name="start-date" required>
        </div>

        <div>
            <label for="end-date">End Date:</label>
            <input type="date" name="end-date" required>
        </div>

        <button type="submit" class="btn">Submit Request</button>
    </form>
</div>



<style>
    /* Basic styling for form */
    .leave-requests {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
        text-align: center;
        color: #333;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="date"],
    textarea,
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .btn {
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
       cursor: pointer;
        transition: background-color 0.3s ease;
    }

   .btn:hover {
        background-color: #0056b3; /* Darker shade on hover */
    }
    
   
    .avater img{
        width: 50px;
        border-radius:10px;
        align-items:center;
        z-index: -1;
    }
</style>

    <!-- Footer Section -->
    <footer class="page-footer">
        <div class="footer-content">
            <div class="avater">
                <img src="img/IMG-20240702-WA0017.jpg " alt="">
            </div>
            <p>Developed by <a href="https://web.facebook.com/pirasski.gh" target="_blank">Ampomah Peter</a></p>
            <p>&copy; 2024 - All Rights Reserved</p>
        </div>
    </footer>

</body>
</html>
