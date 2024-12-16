<?php
// src/user/screens/login.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex min-h-screen">
    <!-- Left Side -->
    <div class="hidden lg:flex w-1/2 bg-green-500  items-center justify-center">
        <img src="../../assets/login.gif" alt="login" class="w-4/7 h-auto object-contain">
    </div>
    
    <!-- Right Side -->
    <div class="flex w-full lg:w-1/2 bg-white items-center justify-center p-8">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold mb-6 text-center text-green-600">Login</h2>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="../php/logincontroller.php" class="space-y-4">
                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your email">
                </div>
                
                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your password">
                </div>
                
                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition duration-200 font-semibold">Login</button>
            </form>
            
            <p class="mt-4 text-center text-gray-600">
                Don't have an account? <a href="signup.php" class="text-green-500 hover:underline">Sign Up</a>
            </p>
        </div>
    </div>
</body>
</html>
