<?php
// src/collector/screens/collector_signup.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collector Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl mb-6 text-center">Collector Signup</h2>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="bg-green-100 text-green-700 p-2 mb-4 rounded">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="../php/collector_signupcontroller.php">
            <input type="text" name="username" placeholder="Username" required class="w-full p-2 mb-4 border rounded">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-4 border rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-4 border rounded">
            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">Sign Up</button>
        </form>
        <p class="mt-4 text-center">Already a collector? <a href="collector_login.php" class="text-green-500">Login</a></p>
    </div>
</body>
</html>
