// Enhanced login status check
function checkLoginStatus() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const username = localStorage.getItem('username');
    const loginBtn = document.getElementById('loginBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const userWelcome = document.getElementById('userWelcome');
    const welcomeMessage = document.getElementById('welcomeMessage');
    
    if (isLoggedIn && username) {
        if (loginBtn) loginBtn.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'block';
        if (userWelcome) userWelcome.textContent = `Welcome, ${username}!`;
        if (welcomeMessage) welcomeMessage.style.display = 'block';
    } else {
        if (loginBtn) loginBtn.style.display = 'block';
        if (logoutBtn) logoutBtn.style.display = 'none';
        if (userWelcome) userWelcome.textContent = '';
        if (welcomeMessage) welcomeMessage.style.display = 'none';
    }
    
    updateCartCount();
}

// This script handles the login status and cart functionality
// Call this function on page load
document.addEventListener('DOMContentLoaded', function() {
    checkLoginStatus();
    
    // Check if redirected from login
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('login') === 'success') {
        alert('Login successful!');
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is logged in
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const username = localStorage.getItem('username');
    
    if (isLoggedIn && username) {
        // Update UI for logged in user
        document.getElementById('loginBtn').style.display = 'none';
        document.getElementById('logoutBtn').style.display = 'block';
        document.getElementById('userWelcome').textContent = `Welcome, ${username}!`;
        document.getElementById('welcomeMessage').style.display = 'block';
    }
    
    // Update cart count
    updateCartCount();
});

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    let totalItems = 0;
    
    cart.forEach(item => {
        totalItems += item.quantity;
    });
    
    document.getElementById("cart-count").textContent = totalItems;
}

function addToCart(productName, productPrice) {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    
    if (!isLoggedIn) {
        alert('Please login first to add items to cart');
        window.location.href = 'login.php';
        return;
    }
    
    let cart = JSON.parse(localStorage.getItem("cart")) || [];
    let product = cart.find(item => item.name === productName);
    
    if (product) {
        product.quantity += 1;
    } else {
        cart.push({ name: productName, price: productPrice, quantity: 1 });
    }
    
    localStorage.setItem("cart", JSON.stringify(cart));
    alert(`${productName} added to cart!`);
    updateCartCount();
}