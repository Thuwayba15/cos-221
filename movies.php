<?php include 'header.php'; ?>




<div id="search-container">
            <input type="text" id="search-input" placeholder="Search...">
            <button id="search-button">Search</button>
        </div>
</header>
=======

</header>


<main>
    <link rel="stylesheet" href="css/movies.css">
    <section id="movies">
        <div class="movie-container">

            <div class="movie">
                <img src="img/movie1.jpeg" alt="Movie 1">
                <p>Fast and Furious 1</p>
                <button class="view-more-button" data-movie-id="1">View More</button>
            </div>
            <div class="movie">
                <img src="img/movie2.jpg" alt="Movie 2">
                <p>Fast and Furious 2</p>
                <button class="view-more-button" data-movie-id="1">View More</button>
            </div>
            <div class="movie">
                <img src="img/movie3.jpeg" alt="Movie 3">
                <p>Fast and Furious 3</p>
                <button class="view-more-button" data-movie-id="1">View More</button>
            </div>
            <div class="movie">
                <img src="img/movie4.jpg" alt="Movie 4">
                <p>Fast and Furious 4</p>
                <button class="view-more-button" data-movie-id="1">View More</button>
            </div>
            <div class="movie">
                <img src="img/movie5.jpg" alt="Movie 5">
                <p>Fast and Furious 5</p>
                <button class="view-more-button" data-movie-id="1">View More</button>
            </div>
            <div class="movie">
                <img src="img/movie6.jpg" alt="Movie 6">
                <p>Fast and Furious 6</p>
                <button class="view-more-button" data-movie-id="1">View More</button>
=======


            <div class="movie">
                <img src="img/movie1.jpeg" alt="Movie 1">
                <p>Fast and Furious 1</p>
                <div class="button-container">
                    <button class="view-more-button" data-movie-id="1">View More</button>
                        <button class="watchlist-button" data-movie-id="1">Add to Watchlist</button>
                        <button class="share-button">Share</button>
                        <button class="like-button" data-movie-id="1">&#x2661;</button>
                    </div>
            </div>


            <div class="movie">
                <img src="img/movie2.jpg" alt="Movie 2">
                <p>Fast and Furious 2</p>
                <div class="button-container">
                    <button class="view-more-button" data-movie-id="2">View More</button>
                        <button class="watchlist-button" data-movie-id="2">Add to Watchlist</button>
                        <button class="share-button">Share</button>
                        <button class="like-button" data-movie-id="1">&#x2661;</button>
                    </div>

            </div>


            <div class="movie">
                <img src="img/movie3.jpeg" alt="Movie 3">
                <p>Fast and Furious 3</p>
                <div class="button-container">
                    <button class="view-more-button" data-movie-id="3">View More</button>
                        <button class="watchlist-button" data-movie-id="3">Add to Watchlist</button>
                        <button class="share-button">Share</button>
                        <button class="like-button" data-movie-id="1">&#x2661;</button>
                    </div>
            </div>

            
            <div class="movie">
                <img src="img/movie4.jpg" alt="Movie 4">
                <p>Fast and Furious 4</p>
                <div class="button-container">
                    <button class="view-more-button" data-movie-id="4">View More</button>
                        <button class="watchlist-button" data-movie-id="4">Add to Watchlist</button>
                        <button class="share-button">Share</button>
                        <button class="like-button" data-movie-id="1">&#x2661;</button>
                    </div>
            </div>


            <div class="movie">
                <img src="img/movie5.jpg" alt="Movie 5">
                <p>Fast and Furious 5</p>
                <div class="button-container">
                    <button class="view-more-button" data-movie-id="5">View More</button>
                        <button class="watchlist-button" data-movie-id="5">Add to Watchlist</button>
                        <button class="share-button">Share</button>
                        <button class="like-button" data-movie-id="1">&#x2661;</button>
                    </div>
            </div>


            <div class="movie">
                <img src="img/movie6.jpg" alt="Movie 6">
                <p>Fast and Furious 6</p>
                <div class="button-container">
                    <button class="view-more-button" data-movie-id="6">View More</button>
                        <button class="watchlist-button" data-movie-id="6">Add to Watchlist</button>
                        <button class="share-button">Share</button>
                        <button class="like-button" data-movie-id="1">&#x2661;</button>
                    </div>
            </div>
        </div>
    </section>

     <script src="js/movies.js"></script>
</main>

<?php include 'footer.php'; ?>
