<?php
// src/collector/screens/collector_login.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collector Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl mb-6 text-center">Collector Login</h2>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="../php/collector_logincontroller.php">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 mb-4 border rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 mb-4 border rounded">
            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">Login</button>
        </form>
        <p class="mt-4 text-center">Not a collector? <a href="collector_signup.php" class="text-green-500">Sign Up</a></p>
    </div>
</body>
</html>
