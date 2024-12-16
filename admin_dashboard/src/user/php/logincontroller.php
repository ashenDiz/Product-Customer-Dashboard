<?php
// src/user/php/logincontroller.php
session_start();
require '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Both email and password are required.";
        header('Location: ../screens/login.php');
        exit();
    }

    // Fetch user from the database with a plain text password comparison
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch();

    // Check if user exists
    if ($user) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: ../screens/customerDashboard.php');
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header('Location: ../screens/login.php');
    }
}
?>
