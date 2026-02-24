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

$address_id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM saved_addresses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $address_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        'id' => $row['id'],
        'full_name' => $row['full_name'],
        'mobile' => $row['mobile'],
        'pincode' => $row['pincode'],
        'address_line1' => $row['address_line1'],
        'city' => $row['city'],
        'state' => $row['state'],
        'landmark' => $row['landmark'],
        'address_type' => $row['address_type'],
        'is_default' => $row['is_default']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Address not found']);
}

$stmt->close();
$conn->close();
?>