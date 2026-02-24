// Additional cart functionality if needed
function showNotification(message, isSuccess = true) {
    const notification = document.createElement('div');
    notification.className = `cart-notification ${isSuccess ? 'success' : 'error'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function showCheckoutProcess() {
    document.getElementById('cart-summary').style.display = 'none';
    document.getElementById('checkout-process').style.display = 'block';
}

function selectPaymentOption(element) {
    // Remove selected class from all options
    const options = document.querySelectorAll('.payment-option');
    options.forEach(option => option.classList.remove('selected'));
    
    // Add selected class to clicked option
    element.classList.add('selected');
}