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

$input = json_decode(file_get_contents('php://input'), true);
$address_id = $input['addressId'] ?? 0;

// For demo purposes, we'll use a static user_id
$user_id = 1;

try {
    $conn->begin_transaction();
    
    // Remove default from all addresses
    $updateSql = "UPDATE saved_addresses SET is_default = FALSE WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $user_id);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Set the selected address as default
    $setSql = "UPDATE saved_addresses SET is_default = TRUE WHERE id = ? AND user_id = ?";
    $setStmt = $conn->prepare($setSql);
    $setStmt->bind_param("ii", $address_id, $user_id);
    $setStmt->execute();
    $setStmt->close();
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Default address updated']);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to update default address']);
}

$conn->close();
?>