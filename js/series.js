// // script.js
// document.addEventListener('DOMContentLoaded', () => {
//     const searchLink = document.getElementById('search-link');
//     const searchContainer = document.getElementById('search-container');

//     searchLink.addEventListener('click', (event) => {
//         event.preventDefault();
//         searchContainer.classList.toggle('hidden');
//     });

//     const searchButton = document.getElementById('search-button');
//     searchButton.addEventListener('click', () => {
//         const query = document.getElementById('search-input').value;
//         performSearch(query);
//     });
// });

// function performSearch(query) {
//     // This function will perform the search using the API.
//     // For now, it will just log the search query to the console.
//     console.log('Searching for:', query);
// }


document.addEventListener('DOMContentLoaded', () => {
    const viewMoreButtons = document.querySelectorAll('.view-more-button');

    viewMoreButtons.forEach(button => {
        button.addEventListener('click', () => {
            const movieId = button.getAttribute('data-movie-id');
            // Here you would typically store the movieId in a way the movie details page can access it
            // For simplicity, we'll use localStorage
            localStorage.setItem('selectedMovieId', movieId);
            window.location.href = 'view_series.php';
        });
    });
});
