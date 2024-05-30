document.addEventListener('DOMContentLoaded', () => {
    const genreSelect = document.getElementById('genre');
    const sortRatingsSelect = document.getElementById('sort-ratings');
    const sortReleaseSelect = document.getElementById('sort-release');
    const sortTitleSelect = document.getElementById('sort-title');
    const recommendationsContainer = document.querySelector('.recommendations-container');

    // Fetch genres from the API and populate the genre dropdown
    fetch('api/genres')
        .then(response => response.json())
        .then(data => {
            data.genres.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre;
                option.textContent = genre;
                genreSelect.appendChild(option);
            });
        });

    // Fetch recommendations based on filters
    function fetchRecommendations() {
        const genre = genreSelect.value;
        const sortRatings = sortRatingsSelect.value;
        const sortRelease = sortReleaseSelect.value;
        const sortTitle = sortTitleSelect.value;

        const query = new URLSearchParams({
            genre,
            sort_ratings: sortRatings,
            sort_release: sortRelease,
            sort_title: sortTitle
        }).toString();

        fetch(`api/recommendations?${query}`)
            .then(response => response.json())
            .then(data => {
                recommendationsContainer.innerHTML = '';
                data.recommendations.forEach(movie => {
                    const movieElement = document.createElement('div');
                    movieElement.classList.add('recommendation');
                    movieElement.innerHTML = `
                        <img src="${movie.image}" alt="${movie.title}">
                        <p>${movie.title}</p>
                        <p>Rating: ${movie.rating}</p>
                    `;
                    recommendationsContainer.appendChild(movieElement);
                });
            });
    }

    // Add event listeners to filters
    genreSelect.addEventListener('change', fetchRecommendations);
    sortRatingsSelect.addEventListener('change', fetchRecommendations);
    sortReleaseSelect.addEventListener('change', fetchRecommendations);
    sortTitleSelect.addEventListener('change', fetchRecommendations);

    // Initial fetch
    fetchRecommendations();
});


document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('filled');
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const viewMoreButtons = document.querySelectorAll('.view-more-button');

    viewMoreButtons.forEach(button => {
        button.addEventListener('click', () => {
            const movieId = button.getAttribute('data-movie-id');
            // Here you would typically store the movieId in a way the movie details page can access it
            // For simplicity, we'll use localStorage
            localStorage.setItem('selectedMovieId', movieId);
            window.location.href = 'view_movie.php';
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const watchlistButtons = document.querySelectorAll('.watchlist-button');

    watchlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('added')) {
                this.textContent = 'Added to Watchlist';
                this.classList.add('added');
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const shareButtons = document.querySelectorAll('.share-button');

    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const shareWindow = window.open('', '', 'width=400,height=300');
            shareWindow.document.write(`
                <html>
                    <head>
                        <title>Share: </title>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
                            img { width: 300px; height: 100px; }
                        </style>
                    </head>
                    <body>
                        <h1>Share: </h1>
                        <img src="img/icons.jpg" alt="Share Icons">
                    </body>
                </html>
            `);
            shareWindow.document.close();
        });
    });
});