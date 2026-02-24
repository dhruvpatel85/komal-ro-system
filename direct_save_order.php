<?php
// direct_save_order.php - SUPER SIMPLE
header('Content-Type: text/plain');

// Get POST data
$order_number = $_POST['order_number'] ?? 'UNKNOWN';
$customer_name = $_POST['customer_name'] ?? '';
$total_amount = $_POST['total_amount'] ?? 0;

// Simple file logging
$log_entry = date('Y-m-d H:i:s') . " - ORDER: $order_number, NAME: $customer_name, AMOUNT: $total_amount\n";
file_put_contents('orders.log', $log_entry, FILE_APPEND);

// Try database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=komal_ro_system', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("INSERT INTO orders SET 
        order_number = ?,
        customer_name = ?,
        customer_email = ?,
        customer_mobile = ?,
        customer_address = ?,
        city = ?,
        state = ?,
        pincode = ?,
        landmark = ?,
        total_amount = ?,
        address_type = ?,
        order_status = 'confirmed',
        payment_status = 'completed',
        payment_method = ?,
        created_at = NOW(),
        updated_at = NOW()");
    
    $stmt->execute([
        $order_number,
        $customer_name,
        $_POST['customer_email'] ?? '',
        $_POST['customer_mobile'] ?? '',
        $_POST['customer_address'] ?? '',
        $_POST['city'] ?? '',
        $_POST['state'] ?? '',
        $_POST['pincode'] ?? '',
        $_POST['landmark'] ?? '',
        $total_amount,
        $_POST['address_type'] ?? 'home',
        $_POST['payment_method'] ?? 'card'
    ]);
    
    echo "SUCCESS";
} catch (Exception $e) {
    // Still log even if database fails
    file_put_contents('orders.log', "DB ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    echo "LOGGED";
}
?>