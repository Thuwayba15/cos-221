document.addEventListener('DOMContentLoaded', () => {
    const seriesId = localStorage.getItem('selectedMovieId');
    if (!seriesId) {
        console.error('No series ID found in localStorage');
        return;
    }

    // Fetch series details from the server
    fetch(`series_details.php?series_id=${seriesId}`)
        .then(response => response.json())
        .then(seriesData => {
            // Populate series details
            document.getElementById('movie-poster').src = seriesData.image;
            document.getElementById('movie-title').textContent = seriesData.title;
            document.getElementById('title').textContent = seriesData.title;
            document.getElementById('language').textContent = seriesData.language;
            document.getElementById('genre').textContent = seriesData.genre;
            document.getElementById('releaseDate').textContent = seriesData.release_date;
            document.getElementById('rating').textContent = seriesData.rating;
            document.getElementById('productionStudio').textContent = seriesData.production_studio;
            document.getElementById('summary').textContent = seriesData.summary;
            document.getElementById('runtime').textContent = seriesData.runtime;
            document.getElementById('actors').innerHTML = seriesData.actors.map(actor => `<a href="view_actor.php?name=${actor}">${actor}</a>`).join(', ');
            document.getElementById('director').textContent = seriesData.director;
            document.getElementById('writers').textContent = seriesData.writers;
            document.getElementById('status').textContent = seriesData.status;
            document.getElementById('seasons').textContent = seriesData.seasons;
            document.getElementById('contentID').textContent = seriesId;  // Using seriesId from localStorage

            // Populate user reviews
            const reviewsContainer = document.getElementById('reviews-container');
            seriesData.reviews.forEach(review => {
                const reviewElement = document.createElement('div');
                reviewElement.classList.add('review');
                reviewElement.innerHTML = `
                    <p><strong>Username:</strong> <span class="username">${review.username}</span></p>
                    <p><strong>Review:</strong> <span class="review-text">${review.review}</span></p>
                    <p><strong>Rating:</strong> <span class="review-rating">${review.rating}/10</span></p>
                `;
                reviewsContainer.appendChild(reviewElement);
            });
        })
        .catch(error => {
            console.error('Error fetching series details:', error);
        });
});