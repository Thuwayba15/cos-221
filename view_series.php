<?php include 'header.php'; ?>

<div id="search-container">
            <input type="text" id="search-input" placeholder="Search...">
            <button id="search-button">Search</button>
        </div>
</header>
    <main>
    <link rel="stylesheet" href="css/view_series.css">
        <section id="movie-view">
            <div class="movie-image">
                <img id="movie-poster" src="img/movie-placeholder.png" alt="Movie Poster">
                <div class="play-button">&#9658;</div>
            </div>
            <h1 id="movie-title">Movie Title</h1>
            <div class="movie-details">
                <p><strong>Title:</strong> <span id="title">Sample Title</span></p>
                <p><strong>Description:</strong> <span id="description">Sample Description</span></p>
                <p><strong>Seasons:</strong> <span id="seasons">5</span></p>
                <p><strong>Genre:</strong> <span id="genre">Action</span></p>
                <p><strong>Actors:</strong> <span id="actors"><a href="view_actor.php">Actor 1</a>, <a href="view_actor.php">Actor 2</a></span></p>
                <p><strong>Director:</strong> <span id="director">Sample Director</span></p>
                <p><strong>IMDB rating:</strong> <span id="imdb-rating">8.5</span></p>
                <p><strong>Release Date:</strong> <span id="release-date">2023-01-01</span></p>
            </div>
            <div class="user-reviews">
                <h2>User Reviews</h2>
                <div class="review">
                    <p><strong>Username:</strong> <span class="username">user1</span></p>
                    <p><strong>Review:</strong> <span class="review-text">I loved this movie</span></p>
                    <p><strong>Rating:</strong> <span class="review-rating">9/10</span></p>
                </div>
                <!-- Additional reviews will be added dynamically -->
            </div>
        </section>
        <script src="js/view_series.js"></script>
    </main>

    <?php include 'footer.php'; ?>