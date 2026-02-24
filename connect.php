<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data - using correct field names
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $passwords = $_POST['passwords'];
    $numbers = $_POST['numbers'];
    $gender = $_POST['gender'];

    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($passwords) || empty($numbers) || empty($gender)) {
        die("All fields are required.");
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Validate phone number
    if (!preg_match("/^[0-9]{10}$/", $numbers)) {
        die("Phone number must be 10 digits.");
    }

    // Validate gender
    $allowedGenders = ['male', 'female', 'other'];
    if (!in_array($gender, $allowedGenders)) {
        die("Invalid gender selection.");
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'web');
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }
    
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO pro (firstName, lastName, email, passwords, numbers, gender) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $passwords, $numbers, $gender);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to index.html after successful registration
        header("Location: index.html");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Close connections
    $stmt->close();
    $conn->close();
} else {
    // If someone tries to access this page directly
    header("Location: webreg1.php");
    exit();
}
?>