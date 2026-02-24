<?php
// update_payment_status.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "komal_ro_system"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data['order_number'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid input data'
    ]);
    exit;
}

$order_number = $conn->real_escape_string($data['order_number']);
$payment_status = $conn->real_escape_string($data['payment_status'] ?? 'success');
$payment_method = $conn->real_escape_string($data['payment_method'] ?? 'card');

try {
    // Check if orders table exists, if not create it
    $check_table = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($check_table->num_rows == 0) {
        // Create orders table
        $create_table_sql = "
        CREATE TABLE orders (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            order_number VARCHAR(50) NOT NULL UNIQUE,
            customer_name VARCHAR(100),
            customer_email VARCHAR(100),
            customer_phone VARCHAR(20),
            total_amount DECIMAL(10,2),
            payment_status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
            payment_method VARCHAR(50),
            delivery_address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if (!$conn->query($create_table_sql)) {
            throw new Exception("Failed to create orders table: " . $conn->error);
        }
    }
    
    // Check if order exists
    $check_order = $conn->prepare("SELECT id FROM orders WHERE order_number = ?");
    $check_order->bind_param("s", $order_number);
    $check_order->execute();
    $result = $check_order->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing order
        $stmt = $conn->prepare("UPDATE orders SET payment_status = ?, payment_method = ?, updated_at = CURRENT_TIMESTAMP WHERE order_number = ?");
        $stmt->bind_param("sss", $payment_status, $payment_method, $order_number);
    } else {
        // Insert new order (for demo purposes)
        $customer_name = "Demo Customer";
        $customer_email = "demo@example.com";
        $customer_phone = "1234567890";
        $total_amount = 17599.00;
        $delivery_address = "Demo address";
        
        $stmt = $conn->prepare("INSERT INTO orders (order_number, customer_name, customer_email, customer_phone, total_amount, payment_status, payment_method, delivery_address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdsss", $order_number, $customer_name, $customer_email, $customer_phone, $total_amount, $payment_status, $payment_method, $delivery_address);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'order_number' => $order_number,
            'payment_status' => $payment_status
        ]);
    } else {
        throw new Exception("Failed to update payment status: " . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    $conn->close();
}
?>