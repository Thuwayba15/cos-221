document.addEventListener('DOMContentLoaded', function() {
    var loginForm = document.getElementById('login-form');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Here, you can perform validation of username and password
        // For simplicity, let's assume the validation is successful

        // Redirect to home.php
        window.location.href = './home.php';
    });
});
