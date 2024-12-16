<?php
// src/user/php/dashboardcontroller.php
session_start();
require '../../../config/database.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Prevent caching of this page to ensure no back navigation after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer & Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<body class="flex">
    <!-- Include Sidebar -->
    <?php include '../components/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page == 'customer_management') {
                include 'customer_management.php';
            } elseif ($page == 'product_management') {
                include 'product_management.php';
            } elseif ($page == 'invoice') {
                include 'invoice.php';
            } else {
                // echo '<h2 class="text-2xl font-bold">Welcome to Customer & Product Management Dashboard</h2>';
            }
        } else {
            echo '<h2 class="text-2xl font-bold">Welcome to Customer & Product Management Dashboard</h2>';
        }
        ?>
    </div>
</body>
</html>
