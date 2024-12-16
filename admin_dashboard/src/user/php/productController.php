<?php
// src\user\php\productController.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin_dashboard/config/database.php';

class ProductController
{
    private $conn;

    public function __construct($pdo)
    {
        $this->conn = $pdo;
    }

    // Function to create a new product
    public function createProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        if (isset($_POST['update'])) {
            $this->updateProduct();
        } else {
            $this->addProduct();
        }
    }

    // Function to add a new product
    private function addProduct()
    {
        $productName = $_POST['productName'] ?? '';
        $price = $_POST['price'] ?? '';
        $quantity = $_POST['quantity'] ?? '';

        if (empty($productName) || empty($price) || empty($quantity)) {
            $this->sendResponse(['status' => 'error', 'message' => 'All fields are required.']);
            return;
        }

        if (!is_numeric($price) || !is_numeric($quantity)) {
            $this->sendResponse(['status' => 'error', 'message' => 'Price and quantity must be numeric values.']);
            return;
        }

        $query = "INSERT INTO products (productName, price, quantity) VALUES (:productName, :price, :quantity)";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ':productName' => htmlspecialchars($productName),
                ':price' => $price,
                ':quantity' => $quantity,
            ]);
            $this->sendResponse(['status' => 'success', 'message' => 'Product created successfully.']);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error creating product: ' . $e->getMessage()]);
        }
    }

    // Function to update an existing product
    private function updateProduct()
    {
        $id = $_POST['id'] ?? '';
        $productName = $_POST['productName'] ?? '';
        $price = $_POST['price'] ?? '';
        $quantity = $_POST['quantity'] ?? '';

        if (empty($productName) || empty($price) || empty($quantity)) {
            $this->sendResponse(['status' => 'error', 'message' => 'All fields are required.']);
            return;
        }

        if (!is_numeric($price) || !is_numeric($quantity)) {
            $this->sendResponse(['status' => 'error', 'message' => 'Price and quantity must be numeric values.']);
            return;
        }

        $query = "UPDATE products SET productName = :productName, price = :price, quantity = :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ':id' => $id,
                ':productName' => htmlspecialchars($productName),
                ':price' => $price,
                ':quantity' => $quantity,
            ]);
            $this->sendResponse(['status' => 'success', 'message' => 'Product updated successfully.']);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error updating product: ' . $e->getMessage()]);
        }
    }

    // Function to delete a product
    public function deleteProduct()
    {
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            $this->sendResponse(['status' => 'error', 'message' => 'Product ID is required.']);
            return;
        }

        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([':id' => $id]);
            $this->sendResponse(['status' => 'success', 'message' => 'Product deleted successfully.']);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error deleting product: ' . $e->getMessage()]);
        }
    }

    // Function to fetch all products
    public function fetchProducts()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendResponse(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $query = "SELECT * FROM products";
        try {
            $stmt = $this->conn->query($query);
            $products = $stmt->fetchAll();
            $this->sendResponse(['status' => 'success', 'data' => $products]);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error fetching products: ' . $e->getMessage()]);
        }
    }

    // Function to send a JSON response
    private function sendResponse($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Initialize the controller with the database connection
$productController = new ProductController($pdo);

// Handle the request based on the method and parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $productController->deleteProduct();
    } else {
        $productController->createProduct();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $productController->fetchProducts();
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unsupported request method.']);
    exit;
}
?>
