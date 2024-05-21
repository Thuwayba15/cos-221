<?php include 'header.php'; ?>

</header>
    <main>
    <link rel="stylesheet" href="css/profile.css">
        <section id="profile">
            <h1>MY PROFILE</h1>
            <div class="profile-info">
                <div class="profile-item">
                    <label for="username">Username</label>
                    <input type="text" id="username" readonly>
                </div>
                <div class="profile-item">
                    <label for="email">Email</label>
                    <input type="email" id="email" readonly>
                </div>
                <div class="profile-item">
                    <label for="subscription">Subscription Plan</label>
                    <input type="text" id="subscription" readonly>
                </div>
            </div>
            <button id="logout-button">Log out</button>
        </section>
        <script src="js/profile.js"></script>
    </main>

    <?php include 'footer.php'; ?>
