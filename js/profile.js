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



document.addEventListener('DOMContentLoaded', function() {
    // Simulated data fetching
    const profileData = {
        username: 'johndoe',
        email: 'johndoe@example.com',
        subscription: 'Premium',
        accountType: 'admin' // This should be 'admin' or 'user'
    };

    // Populate profile fields with data
    document.getElementById('username').value = profileData.username;
    document.getElementById('email').value = profileData.email;
    document.getElementById('subscription').value = profileData.subscription;
    document.getElementById('account-type').value = profileData.accountType.charAt(0).toUpperCase() + profileData.accountType.slice(1); // Capitalize first letter

    // Logout button functionality
    document.getElementById('logout-button').addEventListener('click', function() {
        // Implement logout functionality
        alert('Logged out');
        // Redirect to login page or perform other logout actions
    });
});
