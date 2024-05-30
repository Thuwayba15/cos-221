<?php include 'header.php'; ?>
</header>

    <main>
    <link rel="stylesheet" href="css/view_actor.css">
    <section id="actor-details">
                    <h1 id="actor-name">Actor Name</h1>
                    <div id="actor-image-container">
                        <img src="#" alt="Actor Image" id="actor-image">
                    </div>
                    <p><strong>Country:</strong> <span id="actor-country"></span></p>
                    <p><strong>Birthday:</strong> <span id="actor-birthday"></span></p>
                    <p><strong>Death Day:</strong> <span id="actor-deathday"></span></p>
                    <p><strong>Gender:</strong> <span id="actor-gender"></span></p>
                    <h2>Movies</h2>
                    <div class="movies-container">
                        <!-- Movies will be populated dynamically -->
                    </div>
                </section>
        <script src="js/view_actor.js"></script>
    </main>

<?php include 'footer.php'; ?>


