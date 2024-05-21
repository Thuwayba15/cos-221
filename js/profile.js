document.addEventListener('DOMContentLoaded', () => {
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const subscriptionInput = document.getElementById('subscription');
    const logoutButton = document.getElementById('logout-button');

    // Mock data for user profile
    const userProfile = {
        username: "JohnDoe123",
        email: "johndoe@example.com",
        subscription: "Premium"
    };

    // Function to fetch user profile from the API (mock for now)
    function fetchUserProfile() {
        // Simulate API call
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(userProfile);
            }, 500);
        });
    }

    // Fetch and display user profile data
    fetchUserProfile().then(profile => {
        usernameInput.value = profile.username;
        emailInput.value = profile.email;
        subscriptionInput.value = profile.subscription;
    });

    // Log out function
    logoutButton.addEventListener('click', () => {
        // Clear user session (mock for now)
        console.log('User logged out');
        
        // Redirect to index.html
        window.location.href = 'index.php';
    });
});
