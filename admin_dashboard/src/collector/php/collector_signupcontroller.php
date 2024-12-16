<?php
// src/collector/php/collector_signupcontroller.php
session_start();
require '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../screens/collector_signup.php');
        exit();
    }

    // Insert into collectors table
    $stmt = $pdo->prepare("INSERT INTO collectors (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        $_SESSION['success'] = "Signup successful. Await admin approval.";
        header('Location: ../screens/collector_login.php');
    } catch (PDOException $e) {
        // Handle duplicate entries or other errors
        if ($e->getCode() == 23000) { // Integrity constraint violation
            $_SESSION['error'] = "Username or email already exists.";
        } else {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        header('Location: ../screens/collector_signup.php');
    }
}
?>
