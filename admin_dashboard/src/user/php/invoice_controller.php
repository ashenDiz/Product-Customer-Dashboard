<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin_dashboard/config/database.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data is valid
if (!isset($data['customerId']) || !isset($data['totalPrice']) || !isset($data['products'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

$customerId = $data['customerId'];
$totalPrice = $data['totalPrice'];
$products = $data['products'];

// Ensure that the customer exists
$query = "SELECT COUNT(*) FROM customers WHERE id = :customer_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':customer_id' => $customerId]);
$customerCount = $stmt->fetchColumn();

if ($customerCount == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
    exit();
}

// Start a database transaction to ensure data consistency
$pdo->beginTransaction();

try {
    
    $query = "INSERT INTO invoices (customer_id, total_price) VALUES (:customer_id, :total_price)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':customer_id' => $customerId, ':total_price' => $totalPrice]);

    // Get the last inserted invoice ID
    $invoiceId = $pdo->lastInsertId();

    
    foreach ($products as $product) {
        $productId = $product['productId'];
        $quantity = $product['quantity'];

        // Ensure the product exists and check if enough stock is available
        $query = "SELECT price, quantity FROM products WHERE id = :product_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':product_id' => $productId]);
        $productData = $stmt->fetch();

        if (!$productData) {
            throw new Exception("Product with ID $productId not found");
        }

        // Check if enough quantity is available
        if ($productData['quantity'] < $quantity) {
            throw new Exception("Not enough stock available for product ID $productId");
        }

        // Deduct the stock from the `products` table
        $newQuantity = $productData['quantity'] - $quantity;
        $updateQuery = "UPDATE products SET quantity = :quantity WHERE id = :product_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([':quantity' => $newQuantity, ':product_id' => $productId]);

        // Insert the product into the `invoice_products` table
        $insertQuery = "INSERT INTO invoice_products (invoice_id, product_id, quantity, price) 
                        VALUES (:invoice_id, :product_id, :quantity, :price)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([
            ':invoice_id' => $invoiceId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':price' => $productData['price']
        ]);
    }

    // Commit the transaction
    $pdo->commit();

    // Respond with success
    echo json_encode(['status' => 'success', 'invoice_id' => $invoiceId]);

} catch (Exception $e) {
    // If something goes wrong, roll back the transaction and return the error message
    $pdo->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
