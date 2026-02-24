<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Form - KOMAL RO SYSTEM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .main {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 25px;
            width: 350px;
        }

        .main h1 {
            font-size: 25px;
            color: #2575fc;
            text-align: center;
            margin-bottom: 5px;
        }

        .main h2 {
            color: #4caf50;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .gender-group {
            margin-bottom: 15px;
        }

        .gender-group label {
            display: inline;
            font-weight: normal;
            margin-right: 15px;
        }

        button[type="submit"] {
            padding: 15px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: opacity 0.3s;
        }

        button[type="submit"]:hover {
            opacity: 0.9;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            display: none;
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .error {
            background-color: #ffebee;
            color: #d32f2f;
        }
    </style>
</head>

<body>
    <div class="main">
        <h1>KOMAL RO SYSTEM</h1>
        <h2>Registration Form</h2>
        
        <div id="message" class="message"></div>
        
        <!-- Form now submits directly to connect.php -->
        <form id="registrationForm" action="connect.php" method="post">
            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" required />

            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" required />

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required />

            <label for="passwords">Password:</label>
            <input type="password" id="passwords" name="passwords"
                pattern="^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z00-9])\S{8,}$"
                title="Password must contain at least one number, one alphabet, one symbol, and be at least 8 characters long"
                required />

            <label for="numbers">Contact:</label>
            <input type="text" id="numbers" name="numbers" maxlength="10" required />

            <div class="gender-group">
                <label>Gender:</label>
                <input type="radio" id="male" name="gender" value="male" required> Male
                <input type="radio" id="female" name="gender" value="female"> Female
                <input type="radio" id="other" name="gender" value="other"> Other
            </div>
            
            <button type="submit">Register</button>
        </form>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // Client-side validation only - let the form submit naturally
            
            const firstName = document.getElementById('firstName').value;
            const lastName = document.getElementById('lastName').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('passwords').value;
            const numbers = document.getElementById('numbers').value;
            const gender = document.querySelector('input[name="gender"]:checked');
            
            const messageDiv = document.getElementById('message');
            messageDiv.style.display = 'none';
            
            // Basic validation
            if (!firstName || !lastName || !email || !password || !numbers || !gender) {
                e.preventDefault();
                messageDiv.textContent = 'Please fill in all fields';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
                return false;
            }
            
            // Phone number validation
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(numbers)) {
                e.preventDefault();
                messageDiv.textContent = 'Please enter a valid 10-digit phone number';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
                return false;
            }
            
            // Password pattern validation
            const passwordRegex = /^(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9])\S{8,}$/;
            if (!passwordRegex.test(password)) {
                e.preventDefault();
                messageDiv.textContent = 'Password must contain at least one number, one letter, one symbol, and be at least 8 characters long';
                messageDiv.className = 'message error';
                messageDiv.style.display = 'block';
                return false;
            }
            
            // If all validations pass, the form will submit naturally to connect.php
            return true;
        });
    </script>
</body>

</html>