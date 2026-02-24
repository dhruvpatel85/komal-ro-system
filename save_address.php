<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "komal_ro_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get form data
$fullName = $_POST['fullName'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$pincode = $_POST['pincode'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$landmark = $_POST['landmark'] ?? '';
$addressType = $_POST['addressType'] ?? 'home';
$isDefault = $_POST['isDefault'] ?? false;

// For demo purposes, we'll use a static user_id
$user_id = 1;

// Validate required fields
if (empty($fullName) || empty($mobile) || empty($pincode) || empty($address) || empty($city) || empty($state)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// If this is set as default, remove default from other addresses
if ($isDefault) {
    $updateSql = "UPDATE saved_addresses SET is_default = FALSE WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $user_id);
    $updateStmt->execute();
    $updateStmt->close();
}

// Insert new address
$sql = "INSERT INTO saved_addresses (user_id, full_name, mobile, pincode, address_line1, city, state, landmark, address_type, is_default) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssssi", $user_id, $fullName, $mobile, $pincode, $address, $city, $state, $landmark, $addressType, $isDefault);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Address saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save address']);
}

$stmt->close();
$conn->close();
?>