document.addEventListener('DOMContentLoaded', () => {
    // Mock data
    const movieData = {
        title: "Sample Series",
        description: "This is a sample description of the series.",
        seasons: 5,
        genre: "Action",
        actors: ["Actor 1", "Actor 2", "Actor 3"],
        director: "Sample Director",
        imdbRating: 8.5,
        releaseDate: "2023-01-01",
        image: "img/serie1.jpeg",
        reviews: [
            { username: "user1", review: "I loved this movie", rating: 9 },
            { username: "user2", review: "It was okay", rating: 6 },
            { username: "user3", review: "Not my type", rating: 4 }
        ]
    };

    // Populate movie details
    document.getElementById('movie-poster').src = movieData.image;
    document.getElementById('movie-title').textContent = movieData.title;
    document.getElementById('title').textContent = movieData.title;
    document.getElementById('description').textContent = movieData.description;
    document.getElementById('seasons').textContent = movieData.seasons;
    document.getElementById('genre').textContent = movieData.genre;
    document.getElementById('actors').innerHTML = movieData.actors.map(actor => `<a href="view_actor.html">${actor}</a>`).join(', ');
    document.getElementById('director').textContent = movieData.director;
    document.getElementById('imdb-rating').textContent = movieData.imdbRating;
    document.getElementById('release-date').textContent = movieData.releaseDate;

    // Populate user reviews
    const reviewsContainer = document.querySelector('.user-reviews');
    movieData.reviews.forEach(review => {
        const reviewElement = document.createElement('div');
        reviewElement.classList.add('review');
        reviewElement.innerHTML = `
            <p><strong>Username:</strong> <span class="username">${review.username}</span></p>
            <p><strong>Review:</strong> <span class="review-text">${review.review}</span></p>
            <p><strong>Rating:</strong> <span class="review-rating">${review.rating}/10</span></p>
        `;
        reviewsContainer.appendChild(reviewElement);
    });
});
