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
