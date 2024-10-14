<?php
require 'main.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM employees WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a unique reset token
        $reset_token = bin2hex(random_bytes(16));

        // Store the token in the database with an expiration time (e.g., 1 hour)
        $stmt = $conn->prepare("UPDATE employees SET reset_token = ?, reset_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->execute([$reset_token, $email]);

        // Send reset email with the token link
        // $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $reset_token;
        // $subject = "Password Reset Request";
        // $message = "Click the following link to reset your password: $reset_link";
        // $headers = "From: noreply@yourwebsite.com";

        // mail($email, $subject, $message, $headers);
        echo "A reset link has been sent to your email address.";
    } else {
        echo "No account found with that email.";
    }
}
?>
