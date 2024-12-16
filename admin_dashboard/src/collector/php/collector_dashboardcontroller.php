<?php
session_start();
require '../../../config/database.php';

// Ensure the collector is logged in
if (!isset($_SESSION['collector_id'])) {
    $_SESSION['error'] = 'Please log in to access the dashboard.';
    header('Location: ../screens/collector_login.php');
    exit();
}

$collector_id = $_SESSION['collector_id'];

try {
    // Fetch all requests with their statuses and assigned collectors
    $stmt = $pdo->prepare("
        SELECT 
            r.id, 
            u.username AS user_name, 
            GROUP_CONCAT(w.name SEPARATOR ', ') AS waste_types,
            r.location,
            r.status, 
            r.created_at,
            r.accepted_by AS collector_id
        FROM requests r
        JOIN users u ON r.user_id = u.id
        LEFT JOIN request_wastes rw ON r.id = rw.request_id
        LEFT JOIN waste_types w ON rw.waste_type_id = w.id
        GROUP BY r.id, u.username, r.location, r.status, r.created_at, r.accepted_by
        ORDER BY r.created_at DESC
    ");

    $stmt->execute();
    $requests = $stmt->fetchAll();

} catch (Exception $e) {
    error_log('Collector Dashboard Error: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred while fetching requests.';
    header('Location: ../screens/collector_login.php');
    exit;
}
?>
