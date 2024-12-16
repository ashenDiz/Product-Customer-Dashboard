<?php
// src/collector/php/collector_logincontroller.php
session_start();
require '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Both email and password are required.";
        header('Location: ../screens/collector_login.php');
        exit();
    }

    // Fetch collector from the database
    $stmt = $pdo->prepare("SELECT * FROM collectors WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $collector = $stmt->fetch();

    // Check approval status
    if ($collector) {
        if ($collector['is_approved']) {
            // Set session variables
            $_SESSION['collector_id'] = $collector['id'];
            $_SESSION['collector_username'] = $collector['username'];
            header('Location: ../screens/collector_dashboard.php');
        } else {
            $_SESSION['error'] = "Account pending approval.";
            header('Location: ../screens/collector_login.php');
        }
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header('Location: ../screens/collector_login.php');
    }
}
?>
