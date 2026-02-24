<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['passwords'];

    // Validate input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit();
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'web');
    
    // Check connection
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit();
    }
    
    // Prepare and bind
    $stmt = $conn->prepare("SELECT firstName, lastName, email, passwords FROM pro WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Verify password (assuming plain text for demo)
        if ($password === $row['passwords']) {
            // Set session variables - using consistent naming
            $_SESSION['email'] = $row['email'];
            $_SESSION['username'] = $row['firstName'] . ' ' . $row['lastName']; // Changed to 'username'
            $_SESSION['loggedin'] = true;
            
            // Return success response for AJAX
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful!',
                'username' => $row['firstName'] . ' ' . $row['lastName'] // Changed to 'username'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
    
    $stmt->close();
    $conn->close();
    exit();
}
?>