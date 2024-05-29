document.addEventListener('DOMContentLoaded', function() {
    // Get the register form
    const registerForm = document.getElementById('register-form');
    
    // Add event listener for form submission
    registerForm.addEventListener('submit', function(event) {
        // Prevent the default form submission
        event.preventDefault();
        
        // Perform form validation here if needed
        
        // Redirect the user to home.php
        window.location.href = './home.php';
    });
});
