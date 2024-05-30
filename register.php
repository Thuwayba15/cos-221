<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hoop</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <main>
        <link rel="stylesheet" href="css/register.css">
        <section id="register-section">
            <h1 class="center-heading">Register</h1>
            <form id="register-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" required>
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="subscription-plan">Subscription Plan:</label>
                    <select id="subscription-plan" name="subscription-plan" required>
                        <option value="family">Family</option>
                        <option value="individual">Individual</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bank-account">Bank Account Number:</label>
                    <input type="text" id="bank-account" name="bank-account" required>
                </div>
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
            </form>
        </section>
        <script src="js/register.js"></script>
    </main>
 </body>
 </html>