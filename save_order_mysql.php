<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'komal_ro_system';
$username = 'root'; // Change if needed
$password = ''; // Change if needed

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
        echo json_encode(['success' => false, 'message' => 'Failed to store order in database']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>