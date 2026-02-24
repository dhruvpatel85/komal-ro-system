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
        $error = "All fields are required.";
    } else {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'web');
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection Failed: " . $conn->connect_error);
        }
        
        // Prepare and bind
        $stmt = $conn->prepare("SELECT firstName, lastName, email, passwords FROM pro WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            // Verify password (assuming plain text for demo - use password_hash in production)
            if ($password === $row['passwords']) {
                // Set session variables - using consistent naming
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['firstName'] . ' ' . $row['lastName']; // Changed to 'username'
                $_SESSION['loggedin'] = true;
                
                // Use JavaScript for both localStorage and redirect
                echo "<script>
                    localStorage.setItem('isLoggedIn', 'true');
                    localStorage.setItem('username', '" . $row['firstName'] . " " . $row['lastName'] . "');
                    window.location.href = 'index.html?login=success';
                </script>";
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>KOMAL RO SYSTEM - Login</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            
            body {
                background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .main h2 {
                color: #4caf50;
                text-align: center;
                margin-bottom: 20px;
            }
            
            .login-container {
                background-color: white;
                border-radius: 10px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                width: 100%;
                max-width: 400px;
                padding: 40px;
            }
            
            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .login-header h1 {
                color: #2575fc;
                font-size: 28px;
                margin-bottom: 10px;
            }
            
            .login-header p {
                color: #666;
                font-size: 14px;
            }
            
            .input-group {
                margin-bottom: 20px;
            }
            
            .input-group label {
                display: block;
                margin-bottom: 8px;
                color: #555;
                font-weight: 500;
            }
            
            .input-group input {
                width: 100%;
                padding: 12px 15px;
                border: 1px solid #ddd;
                border-radius: 5px;
                font-size: 14px;
                transition: border-color 0.3s;
            }
            
            .input-group input:focus {
                border-color: #2575fc;
                outline: none;
            }
            
            .show-password {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
            }
            
            .show-password input {
                margin-right: 10px;
                width: auto;
            }
            
            .btn {
                width: 100%;
                padding: 12px;
                background: linear-gradient(to right, #6a11cb, #2575fc);
                border: none;
                border-radius: 5px;
                color: white;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.3s;
            }
            
            .btn:hover {
                opacity: 0.9;
            }
            
            .error-message {
                background-color: #ffebee;
                color: #d32f2f;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .success-message {
                background-color: #e8f5e9;
                color: #2e7d32;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 13px;
                color: #777;
            }
            
            .footer a {
                color: #2575fc;
                text-decoration: none;
            }
            
            .loading-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }
            
            .spinner {
                width: 50px;
                height: 50px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #2575fc;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
    </head>

    <body>
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
        </div>

        <div class="login-container">
            <div class="login-header">
                <h1>KOMAL RO SYSTEM</h1>
                <h2>Login Form</h2>
                <p>Sign in to access your account</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form id="loginForm" method="POST" action="login.php">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="input-group">
                    <label for="passwords">Password</label>
                    <input type="password" id="passwords" name="passwords" placeholder="Enter your password" required>
                </div>

                <div class="show-password">
                    <input type="checkbox" id="showPassword">
                    <label for="showPassword">Show Password</label>
                </div>

                <button type="submit" class="btn" id="loginButton">Login</button>
            </form>

            <div class="footer">
                <p>Don't have an account? <a href="webreg1.php">Create Account</a></p>
            </div>
        </div>

        <script>
            // Show password functionality
            document.getElementById('showPassword').addEventListener('change', function() {
                const passwordField = document.getElementById('passwords');
                if (this.checked) {
                    passwordField.type = 'text';
                } else {
                    passwordField.type = 'password';
                }
            });

            // Form validation and loading indicator
            document.getElementById('loginForm').addEventListener('submit', function(e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('passwords').value;

                if (email.trim() === '' || password.trim() === '') {
                    e.preventDefault();
                    alert('Please fill in all fields');
                } else {
                    // Show loading indicator
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    document.getElementById('loginButton').disabled = true;
                }
            });

            // Check if page was redirected from login attempt
            window.onload = function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('login') === 'failed') {
                    alert('Login failed. Please check your credentials.');
                    // Remove the parameter from URL
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            };
        </script>
    </body>

    </html>