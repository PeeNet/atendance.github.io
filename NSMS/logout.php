<?php
session_start();

// Check if the user has confirmed the logout
if (isset($_POST['confirm_logout'])) {
    // Destroy the session and log out the user
    session_destroy();
    
    // Redirect to the login page
    header('Location: login.html');
    exit;
}
?>



