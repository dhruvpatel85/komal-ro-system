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

// For demo purposes, we'll use a static user_id
// In real application, you'd get this from session
$user_id = 1;

$sql = "SELECT * FROM saved_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$addresses = [];
while ($row = $result->fetch_assoc()) {
    $addresses[] = [
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
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'addresses' => $addresses
]);
?>