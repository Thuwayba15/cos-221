document.addEventListener('DOMContentLoaded', function() {
    var loginForm = document.getElementById('login-form');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;

        var data = {
            email: email,
            password: password
        };

        const username = 'u19072211';
        const password = 'University14';
        const credentials = btoa(username + ':' + password);

        fetch('https://wheatley.cs.up.ac.za/u19072211/COS221/api.php', {  // Adjust this path if your API endpoint is different
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + credentials,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Redirect to home.php
                window.location.href = './home.php';
            } else {
                // Show error message
                var errorMessage = document.createElement('div');
                errorMessage.style.color = 'red';
                errorMessage.textContent = data.data.message;
                loginForm.appendChild(errorMessage);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
});