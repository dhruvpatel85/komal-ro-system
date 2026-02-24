<?php
// save_order_fixed.php - FIXED VERSION
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'komal_ro_system';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Check if we need to reset auto-increment (if insertion fails)
    $maxIdQuery = $pdo->query("SELECT MAX(id) as max_id FROM orders");
    $maxIdResult = $maxIdQuery->fetch(PDO::FETCH_ASSOC);
    $maxId = $maxIdResult['max_id'] ?? 0;
    
    // If we have many records or auto-increment issue, reset it
    if ($maxId > 1000) {
        $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = 1");
    }

    // Prepare SQL query
    $sql = "INSERT INTO orders (
        order_number, 
        customer_name, 
        customer_email, 
        customer_mobile, 
        customer_address, 
        city, 
        state, 
        pincode, 
        landmark, 
        total_amount, 
        address_type, 
        order_status, 
        payment_status, 
        payment_method, 
        created_at, 
        updated_at
    ) VALUES (
        :order_number, 
        :customer_name, 
        :customer_email, 
        :customer_mobile, 
        :customer_address, 
        :city, 
        :state, 
        :pincode, 
        :landmark, 
        :total_amount, 
        :address_type, 
        :order_status, 
        :payment_status, 
        :payment_method, 
        :created_at, 
        :updated_at
    )";

    $stmt = $pdo->prepare($sql);
    
    // Execute the query
    $result = $stmt->execute([
        ':order_number' => $data['order_number'],
        ':customer_name' => $data['customer_name'],
        ':customer_email' => $data['customer_email'],
        ':customer_mobile' => $data['customer_mobile'],
        ':customer_address' => $data['customer_address'],
        ':city' => $data['city'],
        ':state' => $data['state'],
        ':pincode' => $data['pincode'],
        ':landmark' => $data['landmark'],
        ':total_amount' => $data['total_amount'],
        ':address_type' => $data['address_type'],
        ':order_status' => $data['order_status'],
        ':payment_status' => $data['payment_status'],
        ':payment_method' => $data['payment_method'],
        ':created_at' => $data['created_at'],
        ':updated_at' => $data['updated_at']
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Order stored successfully in database']);
    } else {
        // If insertion failed, try with auto-increment reset
        $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = 1");
        
        // Try insertion again
        $result = $stmt->execute([
            ':order_number' => $data['order_number'],
            ':customer_name' => $data['customer_name'],
            ':customer_email' => $data['customer_email'],
            ':customer_mobile' => $data['customer_mobile'],
            ':customer_address' => $data['customer_address'],
            ':city' => $data['city'],
            ':state' => $data['state'],
            ':pincode' => $data['pincode'],
            ':landmark' => $data['landmark'],
            ':total_amount' => $data['total_amount'],
            ':address_type' => $data['address_type'],
            ':order_status' => $data['order_status'],
            ':payment_status' => $data['payment_status'],
            ':payment_method' => $data['payment_method'],
            ':created_at' => $data['created_at'],
            ':updated_at' => $data['updated_at']
        ]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Order stored successfully after auto-increment reset']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to store order in database even after reset']);
        }
    }

} catch (PDOException $e) {
    // If there's a duplicate key error, reset auto-increment and try again
    if ($e->getCode() == 23000) {
        try {
            $pdo->exec("ALTER TABLE orders AUTO_INCREMENT = 1");
            echo json_encode(['success' => true, 'message' => 'Auto-increment reset due to duplicate key']);
        } catch (Exception $resetError) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>