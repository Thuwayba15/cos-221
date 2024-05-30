document.addEventListener('DOMContentLoaded', function() {
    var loginForm = document.getElementById('login-form');

    loginForm.addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevent the default form submission

        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;

        var data = {
            email: email,
            password: password
        };

        const username = 'u19072211';
        const password1 = 'University14';
        const credentials = btoa(username + ':' + password1);

        try {
            const response = await fetch('https://wheatley.cs.up.ac.za/u19072211/COS221/api.php', {  // Adjust this path if your API endpoint is different
                method: 'POST',
                headers: {
                    'Authorization': 'Basic ' + credentials,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            console.log('Response status:', response.status);

            const text = await response.text();
            console.log('Response text:', text);

            if (!text) {
                throw new Error('Empty response from server');
            }

            let responseData;
            try {
                responseData = JSON.parse(text);
            } catch (jsonError) {
                console.error('Failed to parse JSON:', text);
                throw new Error('Failed to parse response as JSON.');
            }

            console.log('Response data:', responseData);

            if (responseData.status === 'success') {
                // Redirect to home.php
                window.location.href = './home.php';
            } else {
                // Show error message
                var errorMessage = document.createElement('div');
                errorMessage.style.color = 'red';
                errorMessage.textContent = responseData.data.message || 'Login failed. Please try again.';
                loginForm.appendChild(errorMessage);
            }
        } catch (error) {
            console.error('Error:', error);
            var errorMessage = document.createElement('div');
            errorMessage.style.color = 'red';
            errorMessage.textContent = 'An error occurred while processing your request.';
            loginForm.appendChild(errorMessage);
        }
    });
});
