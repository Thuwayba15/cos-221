<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hoop</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <main>
        <link rel="stylesheet" href="css/login.css">
        <section id="login-section">
            <h1 class="center-heading">Login</h1>
            <form id="login-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
            </form>
        </section>
        <script src="js/login.js"></script>
    </main>
</body>
</html>