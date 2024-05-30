document.addEventListener('DOMContentLoaded', () => {
    const movieContainer = document.getElementById('movie-container');

    // Function to create movie HTML elements
    const createMovieElement = (movie) => {
        const movieDiv = document.createElement('div');
        movieDiv.classList.add('movie');

        const img = document.createElement('img');
        img.src = movie.image;
        img.alt = movie.title;

        const title = document.createElement('p');
        title.textContent = movie.title;

        const buttonContainer = document.createElement('div');
        buttonContainer.classList.add('button-container');

        const viewMoreButton = document.createElement('button');
        viewMoreButton.classList.add('view-more-button');
        viewMoreButton.setAttribute('data-movie-id', movie.id);
        viewMoreButton.textContent = 'View More';
        viewMoreButton.addEventListener('click', () => {
            localStorage.setItem('selectedMovieId', movie.id);
            window.location.href = 'view_series.php';
        });

        const watchlistButton = document.createElement('button');
        watchlistButton.classList.add('watchlist-button');
        watchlistButton.setAttribute('data-movie-id', movie.id);
        watchlistButton.textContent = 'Add to Watchlist';
        watchlistButton.addEventListener('click', () => {
            if (!watchlistButton.classList.contains('added')) {
                watchlistButton.textContent = 'Added to Watchlist';
                watchlistButton.classList.add('added');
            }
        });

        const shareButton = document.createElement('button');
        shareButton.classList.add('share-button');
        shareButton.textContent = 'Share';
        shareButton.addEventListener('click', () => {
            const shareWindow = window.open('', '', 'width=400,height=300');
            shareWindow.document.write(`
                <html>
                    <head>
                        <title>Share: ${movie.title}</title>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
                            img { width: 300px; height: 100px; }
                        </style>
                    </head>
                    <body>
                        <h1>Share: ${movie.title}</h1>
                        <img src="img/icons.jpg" alt="Share Icons">
                    </body>
                </html>
            `);
            shareWindow.document.close();
        });

        const likeButton = document.createElement('button');
        likeButton.classList.add('like-button');
        likeButton.setAttribute('data-movie-id', movie.id);
        likeButton.innerHTML = '&#x2661;';
        likeButton.addEventListener('click', () => {
            likeButton.classList.toggle('liked');
        });

        buttonContainer.appendChild(viewMoreButton);
        buttonContainer.appendChild(watchlistButton);
        buttonContainer.appendChild(shareButton);
        buttonContainer.appendChild(likeButton);

        movieDiv.appendChild(img);
        movieDiv.appendChild(title);
        movieDiv.appendChild(buttonContainer);

        return movieDiv;
    };

    // Function to fetch movies from the API
    const fetchMovies = async () => {
        try {
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ type: 'getContent' })
            });
            const result = await response.json();
            if (result.status === 'success') {
                const movies = result.data;
                movies.forEach(movie => {
                    const movieElement = createMovieElement(movie);
                    movieContainer.appendChild(movieElement);
                });
            } else {
                console.error(result.data.message);
            }
        } catch (error) {
            console.error('Error fetching movies:', error);
        }
    };

    // Fetch movies on page load
    fetchMovies();
});
