<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "komal_ro_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get ALL form data
$fullName = $_POST['fullName'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$landmark = $_POST['landmark'] ?? '';
$addressType = $_POST['addressType'] ?? 'home';
$paymentMethod = $_POST['paymentMethod'] ?? 'credit_card';
$saveAddress = $_POST['saveAddress'] ?? '0';

// Validate required fields
if (empty($fullName) || empty($mobile) || empty($pincode) || empty($address) || empty($city) || empty($state)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $total_amount = 17599;
    $order_number = 'ON' . date('YmdHis') . rand(100, 999);
    
    // Check if address columns exist in orders table
    $result = $conn->query("SHOW COLUMNS FROM orders LIKE 'customer_address'");
    $has_address_columns = $result->num_rows > 0;
    
    if ($has_address_columns) {
        // Use the address columns
        $sql = "INSERT INTO orders (
            order_number, 
            customer_name, 
            customer_mobile, 
            customer_address,
            city,
            state,
            pincode,
            landmark,
            address_type,
            total_amount, 
            order_status, 
            payment_status, 
            payment_method
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "sssssssssis", 
            $order_number, 
            $fullName, 
            $mobile, 
            $address,
            $city,
            $state,
            $pincode,
            $landmark,
            $addressType,
            $total_amount, 
            $paymentMethod
        );
    } else {
        // Fallback - use only basic columns
        $sql = "INSERT INTO orders (
            order_number, 
            customer_name, 
            customer_mobile, 
            total_amount, 
            order_status, 
            payment_status, 
            payment_method
        ) VALUES (?, ?, ?, ?, 'pending', 'pending', ?)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param(
            "sssis", 
            $order_number, 
            $fullName, 
            $mobile, 
            $total_amount, 
            $paymentMethod
        );
    }
    
    if ($stmt->execute()) {
        $order_id_int = $conn->insert_id;
        $stmt->close();
        
        // Save address to saved_addresses table if requested and table exists
        if ($saveAddress === '1') {
            // Check if saved_addresses table exists
            $table_check = $conn->query("SHOW TABLES LIKE 'saved_addresses'");
            if ($table_check->num_rows > 0) {
                $user_id = 1; // Replace with actual user ID from session
                $saveAddrSql = "INSERT INTO saved_addresses (user_id, full_name, mobile, pincode, address_line1, city, state, landmark, address_type) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $saveAddrStmt = $conn->prepare($saveAddrSql);
                if ($saveAddrStmt) {
                    $saveAddrStmt->bind_param("issssssss", $user_id, $fullName, $mobile, $pincode, $address, $city, $state, $landmark, $addressType);
                    $saveAddrStmt->execute();
                    $saveAddrStmt->close();
                }
            }
            // If table doesn't exist, just continue without saving the address
        }
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id_int,
            'message' => 'Order placed successfully!'
        ]);
        
    } else {
        throw new Exception("Insert failed: " . $stmt->error);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Order processing failed: ' . $e->getMessage()]);
}
// Get payment details
$paymentDetails = json_decode($_POST['paymentDetails'] ?? '{}', true);

// You can store payment details in session or database as needed
$_SESSION['payment_details'] = [
    'order_id' => $order_id_int,
    'payment_method' => $paymentMethod,
    'payment_details' => $paymentDetails,
    'total_amount' => $total_amount
];

$conn->close();
?>