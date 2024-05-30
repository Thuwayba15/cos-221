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

document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('filled');
        });
    });
});

// document.addEventListener('DOMContentLoaded', function() {
//     const likeButtons = document.querySelectorAll('.like-button');
//     const likedMoviesContainer = document.querySelector('.liked-movies-container');

//     likeButtons.forEach(button => {
//         button.addEventListener('click', function() {
//             this.classList.toggle('filled');
//             const movie = this.closest('.movie');
//             const movieId = this.getAttribute('data-movie-id');
            
//             if (this.classList.contains('filled')) {
//                 addToLiked(movie, movieId);
//             } else {
//                 removeFromLiked(movieId);
//             }
//         });
//     });

//     function addToLiked(movie, movieId) {
//         const likedMovie = movie.cloneNode(true);
//         likedMovie.querySelector('.like-button').classList.add('filled');
//         likedMovie.querySelector('.like-button').setAttribute('data-movie-id', movieId);
//         likedMoviesContainer.appendChild(likedMovie);
//     }

//     function removeFromLiked(movieId) {
//         const likedMovie = likedMoviesContainer.querySelector(`.like-button[data-movie-id="${movieId}"]`).closest('.movie');
//         likedMoviesContainer.removeChild(likedMovie);
//     }

//     // Tab navigation
//     const tabs = document.querySelectorAll('nav ul li a');
//     tabs.forEach(tab => {
//         tab.addEventListener('click', function(event) {
//             event.preventDefault();
//             const target = this.getAttribute('href').substring(1);
//             document.querySelectorAll('main > section').forEach(section => {
//                 section.style.display = 'none';
//             });
//             document.getElementById(target).style.display = 'block';
//         });
//     });
// });
