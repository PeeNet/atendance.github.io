
<?php
session_start(); // Start the session
require 'main.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Store user ID and role in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($_SESSION['role'] === 'admin') {
            header('Location: workflow.php'); // Redirect to admin page
        } else {
            header('Location: workflow.php'); // Redirect to user workflow page
        }
        exit();
    } else {
        header('Location: error.php'); // Redirect to error page on failure
        exit();
    }
}
?>

