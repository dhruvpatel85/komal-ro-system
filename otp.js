// Real SMS sending function (replace the simulated one)
function sendOTPViaSMS(mobileNumber, otp) {
    const smsStatus = document.getElementById('smsStatus');
    
    // Show sending status
    smsStatus.textContent = 'Sending verification code...';
    smsStatus.className = 'sms-status';
    smsStatus.style.display = 'block';
    
    // Make API call to your backend
    fetch('send-sms.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            mobile: mobileNumber,
            otp: otp
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            smsStatus.textContent = '✓ Verification code sent successfully via SMS';
            smsStatus.className = 'sms-status sms-success';
        } else {
            smsStatus.textContent = '✗ Failed to send SMS. Please try again.';
            smsStatus.className = 'sms-status sms-error';
        }
    })
    .catch(error => {
        smsStatus.textContent = '✗ Failed to send SMS. Please try again.';
        smsStatus.className = 'sms-status sms-error';
        console.error('Error:', error);
    });
    
    // Hide status message after 5 seconds
    setTimeout(() => {
        smsStatus.style.display = 'none';
    }, 5000);
}