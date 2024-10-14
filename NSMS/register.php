<?php
require 'main.php';  // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $unit = $_POST['unit'];  // Get the unit value from the form

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO employees (name, email, password_hash, role, unit) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHash, $role, $unit]);   
    
    // Redirect to the login page after successful registration
    header('Location: login.html');
}
?>
