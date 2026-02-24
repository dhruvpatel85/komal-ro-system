<?php
// confirmation.php
header('Content-Type: text/html; charset=UTF-8');

$order_id = $_GET['order_id'] ?? 'Unknown';
$payment_status = $_GET['payment_status'] ?? 'success';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - KOMAL RO SYSTEM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .success-message {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        
        .success-message i {
            font-size: 80px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        
        .success-message h2 {
            color: #4caf50;
            margin-bottom: 15px;
        }
        
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            margin: 10px;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header>
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="font-size: 24px; font-weight: bold;">KOMAL RO SYSTEM</div>
            <div>Order Confirmation</div>
        </div>
    </header>
    
    <div class="container">
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <h2>Payment Successful!</h2>
            <p>Thank you for your purchase. Your order has been confirmed.</p>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
                <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($payment_status); ?></p>
                <p><strong>Total Amount:</strong> ₹17,599</p>
                <p><strong>Product:</strong> Cruze Zircon Water Purifier</p>
            </div>
            
            <p>You will receive a confirmation email shortly.</p>
            <p>Your order will be delivered within 5-7 business days.</p>
            
            <div style="margin-top: 30px;">
                <a href="index.html" class="btn">Continue Shopping</a>
                <a href="#" class="btn" onclick="window.print()">Print Receipt</a>
            </div>
        </div>
    </div>
</body>
</html>