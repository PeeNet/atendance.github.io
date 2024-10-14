<?php
require 'main.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the email and new password from the form
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the passwords match
    if ($new_password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the new password
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Update the password
        $stmt = $conn->prepare("UPDATE employees SET password_hash = ? WHERE email = ?");
        $stmt->execute([$new_password_hash, $email]);

        echo "Password reset successful. You can now log in with your new password.";
        header('Location: login.html'); // Redirect to login page after resetting password
        exit();
    } else {
        // If the email is not found
        echo "";
        echo "<script>alert('The email address is not registered.'); window.location.href='reset_password.html';</script>";
   
    }
}
?>
