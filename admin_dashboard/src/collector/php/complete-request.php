<?php
session_start();
require '../../../config/database.php';

// Ensure the collector is logged in
if (!isset($_SESSION['collector_id'])) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Please log in to perform this action.'];
    header('Location: ../screens/collector_login.php');
    exit();
}

$collector_id = $_SESSION['collector_id'];

// Check if the request ID is provided
if (!isset($_POST['request_id'])) {
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Invalid request.'];
    header('Location: ../screens/collector_dashboard.php');
    exit();
}

$request_id = $_POST['request_id'];

// Begin transaction
$pdo->beginTransaction();

try {
    // Verify that the request is accepted by this collector
    $stmt = $pdo->prepare("SELECT status, accepted_by FROM requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if (!$request) {
        throw new Exception('Request not found.');
    }

    if ($request['status'] !== 'accepted') {
        throw new Exception('Request is not in an accepted state.');
    }

    if ($request['accepted_by'] !== $collector_id) {
        throw new Exception('You are not assigned to this request.');
    }

    // Update the request to 'completed'
    $stmt = $pdo->prepare("UPDATE requests SET status = 'completed' WHERE id = ?");
    $stmt->execute([$request_id]);

    $pdo->commit();
    $_SESSION['toast'] = ['type' => 'success', 'message' => 'Request marked as completed.'];
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['toast'] = ['type' => 'error', 'message' => 'Failed to complete request: ' . $e->getMessage()];
}

header('Location: ../screens/collector_dashboard.php');
exit();
?>
