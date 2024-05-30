<?php include 'header.php'; ?>

<div id="search-container">
            <input type="text" id="search-input" placeholder="Search...">
            <button id="search-button">Search</button>
        </div>

</header>

    <main>
        <link rel="stylesheet" href="css/series.css">
        <section id="movies">
            <div class="movie-container">
                <div class="movie">
                    <img src="img/serie1.jpeg" alt="Movie 1">
                    <p>Teen Wolf</p>
                    <button class="view-more-button" data-movie-id="1">View More</button>
                </div>
                <div class="movie">
                    <img src="img/serie2.jpeg" alt="Movie 2">
                    <p>Rick and Morty</p>
                    <button class="view-more-button" data-movie-id="1">View More</button>
                </div>
                <div class="movie">
                    <img src="img/serie3.jpeg" alt="Movie 3">
                    <p>The Flash</p>
                    <button class="view-more-button" data-movie-id="1">View More</button>
                </div>
                <div class="movie">
                    <img src="img/serie4.jpg" alt="Movie 4">
                    <p>Arrow</p>
                    <button class="view-more-button" data-movie-id="1">View More</button>
                </div>
                <div class="movie">
                    <img src="img/serie5.jpg" alt="Movie 5">
                    <p>Gotham</p>
                    <button class="view-more-button" data-movie-id="1">View More</button>
                </div>
                <div class="movie">
                    <img src="img/serie6.jpeg" alt="Movie 6">
                    <p>Stranger Things</p>
                    <button class="view-more-button" data-movie-id="1">View More</button>
                </div>
            </div>
        </section>
        <script src="js/series.js"></script>
    </main>

    <?php include 'footer.php'; ?>
