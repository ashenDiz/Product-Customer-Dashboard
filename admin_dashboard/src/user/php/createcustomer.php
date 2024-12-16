<?php
// Use an absolute path to include the database file 
// src/user/php/createcustomer.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/admin_dashboard/config/database.php';

class CustomerController
{
    private $conn;

    public function __construct($pdo)
    {
        $this->conn = $pdo;
    }

    // Function to create a new customer
    public function createCustomer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendResponse(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (empty($name) || empty($email) || empty($phone)) {
            $this->sendResponse(['status' => 'error', 'message' => 'All fields are required.']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendResponse(['status' => 'error', 'message' => 'Invalid email format.']);
            return;
        }

        $query = "INSERT INTO customers (name, email, phone) VALUES (:name, :email, :phone)";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ':name' => htmlspecialchars($name),
                ':email' => htmlspecialchars($email),
                ':phone' => htmlspecialchars($phone),
            ]);
            $this->sendResponse(['status' => 'success', 'message' => 'Customer created successfully.']);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error creating customer: ' . $e->getMessage()]);
        }
    }

    // Function to fetch all customers
    public function fetchCustomers()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendResponse(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $query = "SELECT * FROM customers";
        try {
            $stmt = $this->conn->query($query);
            $customers = $stmt->fetchAll();
            $this->sendResponse(['status' => 'success', 'data' => $customers]);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error fetching customers: ' . $e->getMessage()]);
        }
    }

    // Function to update a customer
    public function updateCustomer()
    {
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (!$id || empty($name) || empty($email) || empty($phone)) {
            $this->sendResponse(['status' => 'error', 'message' => 'All fields are required.']);
            return;
        }

        $query = "UPDATE customers SET name = :name, email = :email, phone = :phone WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([
                ':id' => $id,
                ':name' => htmlspecialchars($name),
                ':email' => htmlspecialchars($email),
                ':phone' => htmlspecialchars($phone),
            ]);
            $this->sendResponse(['status' => 'success', 'message' => 'Customer updated successfully.']);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error updating customer: ' . $e->getMessage()]);
        }
    }

    public function deleteCustomer()
    {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            $this->sendResponse(['status' => 'error', 'message' => 'Invalid customer ID.']);
            return;
        }

        $query = "DELETE FROM customers WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute([':id' => $id]);
            $this->sendResponse(['status' => 'success', 'message' => 'Customer deleted successfully.']);
        } catch (PDOException $e) {
            $this->sendResponse(['status' => 'error', 'message' => 'Error deleting customer: ' . $e->getMessage()]);
        }
    }

    private function sendResponse($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Initialize the controller with the database connection
$customerController = new CustomerController($pdo);

// Handle POST request for create, update, and delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $customerController->updateCustomer();
    } elseif (isset($_POST['delete'])) {
        $customerController->deleteCustomer();
    } else {
        $customerController->createCustomer();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $customerController->fetchCustomers();
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unsupported request method.']);
    exit;
}
?>
