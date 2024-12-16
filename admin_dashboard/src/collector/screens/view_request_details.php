<?php
session_start();
require '../../../config/database.php';

// Ensure the collector is logged in
if (!isset($_SESSION['collector_id'])) {
    $_SESSION['error'] = 'Please log in to view request details.';
    header('Location: collector_login.php');
    exit();
}

$collector_id = $_SESSION['collector_id'];

// Retrieve the request details based on the request ID passed from the dashboard
if (isset($_GET['request_id']) && is_numeric($_GET['request_id'])) {
    $request_id = $_GET['request_id'];

    // Use a JOIN to get user name, waste types, and their prices
    $stmt = $pdo->prepare("
        SELECT r.*, u.username AS user_name, 
               GROUP_CONCAT(w.name) AS waste_types, 
               GROUP_CONCAT(w.price) AS waste_prices
        FROM requests r
        JOIN users u ON r.user_id = u.id
        LEFT JOIN request_wastes rw ON r.id = rw.request_id
        LEFT JOIN waste_types w ON rw.waste_type_id = w.id
        WHERE r.id = ?
        GROUP BY r.id
    ");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        $_SESSION['error'] = 'Request not found.';
        header('Location: collector_dashboard.php');
        exit();
    }

    // Calculate the total price
    $waste_prices = explode(',', $request['waste_prices']);
    $total_price = array_sum($waste_prices);
} else {
    $_SESSION['error'] = 'Invalid request ID.';
    header('Location: collector_dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-green-600 p-4 flex justify-between items-center shadow-md">
        <h1 class="text-white text-xl font-bold">E-Waste Management - Request Details</h1>
        <a href="collector_dashboard.php" class="text-white bg-blue-500 px-3 py-1 rounded hover:bg-blue-600">Back to Dashboard</a>
    </header>

    <main class="p-6">
    <div class="mt-16 bg-white p-8 rounded-md shadow-xl max-w-4xl mx-auto space-y-6">
        <h2 class="text-3xl font-semibold text-center text-gray-900 mb-6">Request Details</h2>
        
        <div class="grid grid-cols-2 gap-6">
            <!-- User Name -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">User Name:</p>
                <p class="text-lg text-gray-800"><?= htmlspecialchars($request['user_name']); ?></p>
            </div>
            
            <!-- Location -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Location:</p>
                <p class="text-lg text-gray-800"><?= htmlspecialchars($request['location']); ?></p>
            </div>

            <!-- Contact Number -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Contact Number:</p>
                <p class="text-lg text-gray-800"><?= htmlspecialchars($request['contact_number']); ?></p>
            </div>

            <!-- Waste Types -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Waste Types:</p>
                <div class="text-lg text-gray-800 overflow-auto whitespace-nowrap">
                    <?= implode(', ', explode(',', $request['waste_types'])); ?>
                </div>
            </div>
            
            <!-- Waste Prices -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Waste Prices:</p>
                <div class="text-lg text-gray-800 overflow-auto whitespace-nowrap">
                    LKR: <?= implode(', ', $waste_prices); ?>
                </div>
            </div>

            <!-- Total Price -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Total Price:</p>
                <p class="text-xl font-semibold text-red-600">LKR <?= number_format($total_price, 2); ?></p>
            </div>

            <!-- Status -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Status:</p>
                <p class="text-lg text-pink-800"><?= ucfirst(htmlspecialchars($request['status'])); ?></p>
            </div>

            <!-- Created At -->
            <div class="flex flex-col">
                <p class="text-sm font-semibold text-gray-600">Created At:</p>
                <p class="text-md text-gray-800"><?= htmlspecialchars($request['created_at']); ?></p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <?php if ($request['status'] === 'pending'): ?>
                <form action="../php/accept-request.php" method="POST">
                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['id']); ?>">
                    <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition duration-300 transform hover:scale-105">Accept Request</button>
                </form>
            <?php elseif ($request['status'] === 'accepted' && $request['accepted_by'] == $collector_id): ?>
                <form action="../php/complete-request.php" method="POST">
                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($request['id']); ?>">
                    <button type="submit" class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition duration-300 transform hover:scale-105">Mark as Completed</button>
                </form>
            <?php else: ?>
                <button class="bg-gray-500 text-white px-6 py-3 rounded-lg cursor-not-allowed">Action Not Available</button>
            <?php endif; ?>
        </div>
    </div>


    </main>
</body>
</html>
