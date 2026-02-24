<?php
// process_order.php - ALTERNATIVE VERSION
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'komal_ro_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'No data received']);
        exit;
    }
    
    // Extract data
    $fullName = $input['fullName'] ?? '';
    $mobile = $input['mobile'] ?? '';
    $pincode = $input['pincode'] ?? '';
    $address = $input['address'] ?? '';
    $city = $input['city'] ?? '';
    $state = $input['state'] ?? '';
    $landmark = $input['landmark'] ?? '';
    $addressType = $input['addressType'] ?? 'home';
    
    // Generate order number
    $orderNumber = 'KRS' . date('YmdHis') . rand(100, 999);
    $totalAmount = 17599;
    
    // First, let's check the actual column names in the orders table
    $stmt = $pdo->query("DESCRIBE orders");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    error_log("Available columns in orders table: " . implode(', ', $columns));
    
    // Build the SQL query dynamically based on available columns
    $availableColumns = [
        'order_number', 'customer_name', 'customer_mobile', 'customer_address',
        'city', 'state', 'pincode', 'landmark', 'total_amount', 'address_type',
        'order_status', 'payment_status', 'payment_method'
    ];
    
    $insertColumns = [];
    $insertValues = [];
    $placeholders = [];
    
    // Add mandatory fields
    $insertColumns[] = 'order_number';
    $insertValues[] = $orderNumber;
    $placeholders[] = '?';
    
    // Map input data to available columns
    $columnMapping = [
        'fullName' => 'customer_name',
        'mobile' => 'customer_mobile', 
        'address' => 'customer_address',
        'city' => 'city',
        'state' => 'state',
        'pincode' => 'pincode',
        'landmark' => 'landmark',
        'addressType' => 'address_type'
    ];
    
    foreach ($columnMapping as $inputKey => $dbColumn) {
        if (in_array($dbColumn, $columns)) {
            $insertColumns[] = $dbColumn;
            $insertValues[] = $input[$inputKey] ?? '';
            $placeholders[] = '?';
        }
    }
    
    // Add total amount if column exists
    if (in_array('total_amount', $columns)) {
        $insertColumns[] = 'total_amount';
        $insertValues[] = $totalAmount;
        $placeholders[] = '?';
    }
    
    // Add status fields if they exist
    if (in_array('order_status', $columns)) {
        $insertColumns[] = 'order_status';
        $insertValues[] = 'pending';
        $placeholders[] = '?';
    }
    
    if (in_array('payment_status', $columns)) {
        $insertColumns[] = 'payment_status';
        $insertValues[] = 'pending';
        $placeholders[] = '?';
    }
    
    if (in_array('payment_method', $columns)) {
        $insertColumns[] = 'payment_method';
        $insertValues[] = 'pending';
        $placeholders[] = '?';
    }
    
    // Add timestamps if they exist
    if (in_array('created_at', $columns)) {
        $insertColumns[] = 'created_at';
        $insertValues[] = date('Y-m-d H:i:s');
        $placeholders[] = '?';
    }
    
    if (in_array('updated_at', $columns)) {
        $insertColumns[] = 'updated_at';
        $insertValues[] = date('Y-m-d H:i:s');
        $placeholders[] = '?';
    }
    
    // Build and execute the insert query
    $sql = "INSERT INTO orders (" . implode(', ', $insertColumns) . ") 
            VALUES (" . implode(', ', $placeholders) . ")";
    
    error_log("Executing SQL: " . $sql);
    error_log("With values: " . implode(', ', $insertValues));
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($insertValues);
    
    $orderId = $pdo->lastInsertId();
    
    $_SESSION['last_order_id'] = $orderNumber;
    
    echo json_encode([
        'success' => true,
        'order_id' => $orderNumber,
        'message' => 'Order created successfully'
    ]);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'debug' => 'Check server error logs for details'
    ]);
}
?>