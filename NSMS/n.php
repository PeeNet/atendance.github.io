<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Employee Records</title>
    <link rel="stylesheet" href="admin.css"> <!-- Your CSS file -->
    <style>
        body {
            display: flex; /* Use flex to create a layout */
            font-family: Arial, sans-serif; /* Basic font for the page */
        }

        /* Sidebar styling */
        .sidebar {
            width: 200px; /* Width of the sidebar */
            background-color: #3498db; /* Background color */
            color: white; /* Text color */
            padding: 20px; /* Padding inside sidebar */
            height: 100vh; /* Full height */
            position: fixed; /* Fixed position */
        }

        .sidebar h2 {
            margin: 0; /* Remove default margin */
            font-size: 1.5em; /* Heading size */
        }

        .sidebar a {
            display: block; /* Block display for links */
            color: white; /* Link color */
            padding: 10px; /* Padding for links */
            text-decoration: none; /* Remove underline */
            border-radius: 5px; /* Rounded corners */
            margin: 10px 0; /* Margin between links */
        }

        .sidebar a:hover {
            background-color: #2980b9; /* Darker shade on hover */
        }

        /* Main content styling */
        .main-content {
            margin-left: 220px; /* Space for sidebar */
            padding: 20px; /* Padding for main content */
            flex-grow: 1; /* Grow to fill remaining space */
        }

        /* Other styles as before */
        /* ... (keep your existing styles here) ... */
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php">Home</a>
        <a href="admin.php">Admin</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="main-content">
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
                    </div>
                </a>
            </div>

            <form method="GET" action="" class="filter-form">
                <label for="unit" class="filter-label">Filter by Unit:</label>
                <select name="unit" id="unit" class="filter-select">
                    <option value="">All Units</option>
                    <option value="ICU">IC
