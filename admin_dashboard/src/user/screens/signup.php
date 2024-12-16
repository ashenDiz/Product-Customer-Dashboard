<?php
// src/user/screens/signup.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex min-h-screen">
    <!-- Left Side -->
    <div class="hidden lg:flex w-1/2 items-center justify-center">
        <img src="../../assets/sign up.gif" alt="E-Waste Management" class="w-4/7 h-auto object-contain">
    </div>
    
    <!-- Right Side -->
    <div class="flex w-full lg:w-1/2 bg-white items-center justify-center p-8">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold mb-6 text-center text-green-600">Signup</h2>
            
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
            
            <form method="POST" action="../php/signupcontroller.php" class="space-y-4">
                <div>
                    <label for="username" class="block text-gray-700 font-semibold mb-2">Username</label>
                    <input type="text" id="username" name="username" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your username">
                </div>
                
                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your email">
                </div>
                
                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your password">
                </div>
                
                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600 transition duration-200 font-semibold">Sign Up</button>
            </form>
            
            <p class="mt-4 text-center text-gray-600">
                Already have an account? <a href="login.php" class="text-green-500 hover:underline">Login</a>
            </p>
        </div>
    </div>
</body>
</html>
