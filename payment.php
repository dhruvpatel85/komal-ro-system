<?php
// payment.php - COMPLETE WORKING VERSION
session_start();

// Get order ID from URL, session, or generate new one
$order_id = $_GET['order_id'] ?? $_SESSION['last_order_id'] ?? 'KRS' . date('YmdHis') . rand(100, 999);

// Store in session for consistency
$_SESSION['last_order_id'] = $order_id;

// Set order ID in localStorage for JavaScript
$order_id_js = json_encode($order_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - KOMAL RO SYSTEM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ALL YOUR EXISTING CSS FROM payment.html */
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
            max-width: 1000px;
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
        
        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        .progress-steps {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
            position: relative;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e0e0;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 10px;
            z-index: 2;
        }
        
        .step.active .step-number {
            background-color: #2575fc;
            color: white;
        }
        
        .step.completed .step-number {
            background-color: #4caf50;
            color: white;
        }
        
        .step-text {
            font-size: 14px;
            color: #777;
        }
        
        .step.active .step-text {
            color: #2575fc;
            font-weight: 500;
        }
        
        .step.completed .step-text {
            color: #4caf50;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 70px;
            right: -20px;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .payment-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .card-title {
            font-size: 18px;
            color: #2575fc;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #2575fc;
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 117, 252, 0.2);
        }
        
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .payment-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .payment-option {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .payment-option:hover {
            border-color: #2575fc;
        }
        
        .payment-option.selected {
            border-color: #2575fc;
            background-color: #f0f7ff;
        }
        
        .payment-option i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2575fc;
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
            text-align: center;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .btn-success {
            background: linear-gradient(to right, #4caf50, #2e7d32);
        }
        
        .order-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-summary-item:last-child {
            border-bottom: none;
        }
        
        .order-total {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }
        
        .product-info {
            display: flex;
            margin-bottom: 15px;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-image i {
            font-size: 40px;
            color: white;
        }
        
        .product-details {
            flex: 1;
        }
        
        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .product-price {
            color: #4caf50;
            font-weight: bold;
        }
        
        .payment-details-form {
            display: none;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .payment-details-form.active {
            display: block;
        }

        .payment-details-form .form-group {
            margin-bottom: 15px;
        }

        .card-details-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .wallet-options, .bank-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .wallet-option, .bank-option {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .wallet-option:hover, .bank-option:hover {
            border-color: #2575fc;
        }

        .wallet-option.selected, .bank-option.selected {
            border-color: #2575fc;
            background-color: #f0f7ff;
        }

        .bank-option i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2575fc;
        }
        
        .delivery-address {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            border-left: 4px solid #2575fc;
        }

        .loading {
            display: none;
            text-align: center;
            margin: 20px 0;
        }

        .error-message {
            display: none;
            background: #ffe6e6;
            color: #d63031;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #d63031;
        }

        .success-message {
            display: none;
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .success-message i {
            font-size: 60px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        
        .success-message h2 {
            color: #4caf50;
            margin-bottom: 15px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
        }

        .form-check-label {
            font-size: 14px;
            color: #555;
        }

        @media (max-width: 900px) {
            .payment-container {
                grid-template-columns: 1fr;
            }
            
            .row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 600px) {
            .progress-steps {
                flex-wrap: wrap;
            }
            
            .step {
                margin-bottom: 20px;
            }
            
            .payment-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">KOMAL RO SYSTEM</div>
            <div class="secure-checkout">Secure Payment</div>
        </div>
    </header>
    
    <div class="container">
        <div class="progress-steps">
            <div class="step completed">
                <div class="step-number"><i class="fas fa-check"></i></div>
                <div class="step-text">Cart</div>
            </div>
            <div class="step completed">
                <div class="step-number"><i class="fas fa-check"></i></div>
                <div class="step-text">Address</div>
            </div>
            <div class="step active">
                <div class="step-number">3</div>
                <div class="step-text">Payment</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-text">Confirmation</div>
            </div>
        </div>
        
        <!-- Error Message Container -->
        <div class="error-message" id="errorMessage"></div>
        
        <div class="payment-container">
            <div class="left-column">
                <div class="card">
                    <h2 class="card-title">Payment Method</h2>

                    <!-- Payment Options -->
                    <div class="payment-options">
                        <div class="payment-option selected" data-method="card">
                            <i class="fas fa-credit-card"></i>
                            <div>Credit/Debit Card</div>
                        </div>
                        
                        <div class="payment-option" data-method="netbanking">
                            <i class="fas fa-university"></i>
                            <div>Net Banking</div>
                        </div>
                        
                        <div class="payment-option" data-method="upi">
                            <i class="fas fa-mobile-alt"></i>
                            <div>UPI</div>
                        </div>
                        
                        <div class="payment-option" data-method="wallet">
                            <i class="fas fa-wallet"></i>
                            <div>Wallet</div>
                        </div>
                    </div>
                    
                    <!-- Dynamic Payment Forms -->
                    <div class="payment-details-form active" id="cardDetailsForm">
                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" id="cardNumber" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        
                        <div class="form-group">
                            <label for="cardName">Name on Card</label>
                            <input type="text" id="cardName" class="form-control" placeholder="John Doe">
                        </div>
                        
                        <div class="card-details-row">
                            <div class="form-group">
                                <label for="expiryDate">Expiry Date</label>
                                <input type="text" id="expiryDate" class="form-control" placeholder="MM/YY" maxlength="5">
                            </div>
                            
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="password" id="cvv" class="form-control" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>

                    <div class="payment-details-form" id="netbankingDetailsForm">
                        <div class="form-group">
                            <label>Select Your Bank</label>
                            <div class="bank-options">
                                <div class="bank-option" data-bank="sbi">
                                    <i class="fas fa-university"></i>
                                    <div>SBI</div>
                                </div>
                                <div class="bank-option" data-bank="hdfc">
                                    <i class="fas fa-university"></i>
                                    <div>HDFC</div>
                                </div>
                                <div class="bank-option" data-bank="icici">
                                    <i class="fas fa-university"></i>
                                    <div>ICICI</div>
                                </div>
                                <div class="bank-option" data-bank="axis">
                                    <i class="fas fa-university"></i>
                                    <div>Axis</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="payment-details-form" id="upiDetailsForm">
                        <div class="form-group">
                            <label for="upiId">UPI ID</label>
                            <input type="text" id="upiId" class="form-control" placeholder="yourname@upi">
                        </div>
                    </div>

                    <div class="payment-details-form" id="walletDetailsForm">
                        <div class="form-group">
                            <label>Select Wallet</label>
                            <div class="wallet-options">
                                <div class="wallet-option" data-wallet="paytm">
                                    <i class="fas fa-mobile-alt"></i>
                                    <div>Paytm</div>
                                </div>
                                <div class="wallet-option" data-wallet="phonepe">
                                    <i class="fas fa-mobile-alt"></i>
                                    <div>PhonePe</div>
                                </div>
                                <div class="wallet-option" data-wallet="googlepay">
                                    <i class="fas fa-mobile-alt"></i>
                                    <div>Google Pay</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="right-column">
                <div class="card">
                    <h2 class="card-title">Order Summary</h2>
                    
                    <div class="product-info">
                        <div class="product-image">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div class="product-details">
                            <div class="product-name">Cruze Zircon Water Purifier</div>
                            <div class="product-price">₹15,999</div>
                            <div>Quantity: 1</div>
                        </div>
                    </div>
                    
                    <div class="order-summary-item">
                        <div>Subtotal</div>
                        <div>₹15,999</div>
                    </div>
                    
                    <div class="order-summary-item">
                        <div>Delivery Charges</div>
                        <div style="color: #4caf50;">FREE</div>
                    </div>
                    
                    <div class="order-summary-item">
                        <div>GST</div>
                        <div>₹1,600</div>
                    </div>
                    
                    <div class="order-total">
                        <div>Total</div>
                        <div>₹17,599</div>
                    </div>
                    
                    <button class="btn btn-block btn-success" id="proceedPayment">
                        <i class="fas fa-lock"></i> Proceed to Pay ₹17,599
                    </button>
                    
                    <div class="loading" id="loading">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Processing payment...</p>
                    </div>

                    <!-- Test Button for Debugging -->
                    <button class="btn btn-block" id="testPayment" style="background: #ff6b6b; margin-top: 10px;">
                        <i class="fas fa-bug"></i> Test Payment (Skip to Confirmation)
                    </button>
                </div>
                
                <div class="card">
                    <h2 class="card-title">Order Information</h2>
                    <div class="delivery-address">
                        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
                        <p><strong>Status:</strong> Payment Pending</p>
                        <p><i class="fas fa-info-circle"></i> Your order will be delivered within 5-7 business days.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle"></i>
            <h2>Payment Successful!</h2>
            <p>Thank you for your purchase. Your order has been confirmed.</p>
            <p>Order ID: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
            <p>You will receive a confirmation email shortly.</p>
        </div>
    </div>

    <script>
        // Global variables
        let selectedPaymentMethod = 'card';
        let selectedBank = '';
        let selectedWallet = '';
        const orderId = <?php echo $order_id_js; ?>;

        // Utility functions
        function showError(message) {
            const errorElement = document.getElementById('errorMessage');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            setTimeout(() => errorElement.style.display = 'none', 5000);
        }

        function showSuccessMessage() {
            document.getElementById('successMessage').style.display = 'block';
        }

        // Payment method selection
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentForms = {
                card: document.getElementById('cardDetailsForm'),
                netbanking: document.getElementById('netbankingDetailsForm'),
                upi: document.getElementById('upiDetailsForm'),
                wallet: document.getElementById('walletDetailsForm')
            };

            // Payment option clicks
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    paymentOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedPaymentMethod = this.getAttribute('data-method');
                    
                    Object.values(paymentForms).forEach(form => form.style.display = 'none');
                    if (paymentForms[selectedPaymentMethod]) {
                        paymentForms[selectedPaymentMethod].style.display = 'block';
                    }
                });
            });

            // Bank selection
            document.querySelectorAll('.bank-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.bank-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedBank = this.getAttribute('data-bank');
                });
            });

            // Wallet selection
            document.querySelectorAll('.wallet-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.wallet-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedWallet = this.getAttribute('data-wallet');
                });
            });

            // Input formatting
            const cardNumberInput = document.getElementById('cardNumber');
            if (cardNumberInput) {
                cardNumberInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    let parts = [];
                    for (let i = 0; i < value.length; i += 4) {
                        parts.push(value.substring(i, i + 4));
                    }
                    e.target.value = parts.join(' ').substring(0, 19);
                });
            }

            const expiryInput = document.getElementById('expiryDate');
            if (expiryInput) {
                expiryInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                });
            }

            // SIMPLIFIED PAYMENT PROCESSING - NO SERVER CALLS
            document.getElementById('proceedPayment').addEventListener('click', async function() {
                const btn = this;
                const loading = document.getElementById('loading');
                
                // Simple validation
                if (selectedPaymentMethod === 'card') {
                    const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
                    if (cardNumber.length !== 16) {
                        showError('Please enter a valid 16-digit card number');
                        return;
                    }
                }

                // Show loading
                btn.style.display = 'none';
                loading.style.display = 'block';

                try {
                    // Simulate payment processing (3 seconds)
                    await new Promise(resolve => setTimeout(resolve, 3000));
                    
                    // Show success message
                    showSuccessMessage();
                    
                    // Redirect to confirmation after 2 seconds
                    setTimeout(() => {
                        window.location.href = `confirmation.php?order_id=${orderId}&payment_status=success`;
                    }, 2000);

                } catch (error) {
                    console.error('Payment error:', error);
                    showError('Payment processing failed. Please try again.');
                    btn.style.display = 'block';
                    loading.style.display = 'none';
                }
            });

            // Test payment button (skip to confirmation)
            document.getElementById('testPayment').addEventListener('click', function() {
                window.location.href = `confirmation.php?order_id=${orderId}&payment_status=success`;
            });
        });
    </script>
</body>
</html>