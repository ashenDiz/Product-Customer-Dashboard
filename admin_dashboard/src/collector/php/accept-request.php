<?php
// src/collector/php/accept-request.php
session_start();
require '../../../config/database.php';

// Ensure the collector is logged in
if (!isset($_SESSION['collector_id'])) {
    $_SESSION['error'] = 'Please log in to accept requests.';
    header('Location: ../screens/collector_login.php');
    exit();
}

$collector_id = $_SESSION['collector_id'];

// Check if the request_id is set and is valid
if (isset($_POST['request_id']) && is_numeric($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    try {
        // Update the request to mark it as accepted and associate it with the collector
        $stmt = $pdo->prepare("UPDATE requests SET status = 'accepted', accepted_by = ? WHERE id = ?");
        $stmt->execute([$collector_id, $request_id]);

        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Request accepted successfully.'];
        header('Location: ../screens/collector_dashboard.php');
        exit();
    } catch (Exception $e) {
        error_log('Accept Request Error: ' . $e->getMessage());
        $_SESSION['toast'] = ['type' => 'error', 'message' => 'An error occurred while accepting the request.'];
        header('Location: ../screens/collector_dashboard.php');
        exit();
    }
} else {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid request ID.'];
    header('Location: ../screens/collector_dashboard.php');
    exit();
}
?>
