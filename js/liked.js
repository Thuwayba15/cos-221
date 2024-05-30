document.addEventListener('DOMContentLoaded', function() {
    const watchlistContainer = document.querySelector('.watchlist-container');

    let watchlist = JSON.parse(localStorage.getItem('watchlist')) || [];

    watchlist.forEach(movieId => {
        // Assuming you have a function to get movie details by ID
        const movieDetails = getMovieDetailsById(movieId);

        const movieElement = document.createElement('div');
        movieElement.classList.add('movie');
        movieElement.setAttribute('data-movie-id', movieId);
        movieElement.innerHTML = `
            <img src="${movieDetails.image}" alt="${movieDetails.title}">
            <p>${movieDetails.title}</p>
            <button class="view-more-button" data-movie-id="${movieId}">View More</button>
            <button class="remove-button" data-movie-id="${movieId}">Remove</button>
        `;

        watchlistContainer.appendChild(movieElement);
    });

    // Add event listeners to remove buttons on the watchlist page
    const removeButtons = watchlistContainer.querySelectorAll('.remove-button');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const movieId = this.getAttribute('data-movie-id');
            const movieElement = document.querySelector(`.movie[data-movie-id="${movieId}"]`);

            // Remove movie from the DOM
            movieElement.remove();

            // Update localStorage
            watchlist = watchlist.filter(id => id !== movieId);
            localStorage.setItem('watchlist', JSON.stringify(watchlist));
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

function getMovieDetailsById(id) {
    // Placeholder function to get movie details by ID
    // Replace this with your actual implementation
    const movieDatabase = {
        1: { title: 'Fast and Furious 1', image: 'img/movie1.jpeg' },
        2: { title: 'Fast and Furious 2', image: 'img/movie2.jpg' },
        3: { title: 'Fast and Furious 3', image: 'img/movie3.jpeg' },
        4: { title: 'Fast and Furious 4', image: 'img/movie4.jpg' },
        5: { title: 'Fast and Furious 5', image: 'img/movie5.jpg' },
        6: { title: 'Fast and Furious 6', image: 'img/movie6.jpg' },
    };
    return movieDatabase[id];
}
