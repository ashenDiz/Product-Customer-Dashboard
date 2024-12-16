<?php
// src/user/php/signupcontroller.php
session_start();
require '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation (you can enhance this)
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../screens/signup.php');
        exit();
    }

    // Insert into users table without hashing
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        $_SESSION['success'] = "Signup successful. Please log in.";
        header('Location: ../screens/login.php');
    } catch (PDOException $e) {
        // Handle duplicate entries or other errors
        if ($e->getCode() == 23000) { // Integrity constraint violation
            $_SESSION['error'] = "Username or email already exists.";
        } else {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        header('Location: ../screens/signup.php');
    }
}
?>
