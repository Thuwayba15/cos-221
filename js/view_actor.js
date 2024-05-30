document.addEventListener('DOMContentLoaded', () => {
    const actorNameElement = document.getElementById('actor-name');
    const moviesContainer = document.querySelector('.movies-container');

    // Mock data for now
    const actorData = {
        name: "John Doe",
        movies: [
            { title: "Fast and Furious 1", image: "img/movie1.jpeg" },
            { title: "Fast and Furious 2", image: "img/movie2.jpg" },
            { title: "Fast and Furious 3", image: "img/movie3.jpeg" }
        ]
    };

    // Function to fetch actor data from API (mock for now)
    function fetchActorData(actorId) {
        // Simulate API call
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(actorData);
            }, 500);
        });
    }

    // Get actor ID from URL (for demonstration purposes, we'll use a static ID)
    const actorId = "123"; // This would be dynamically determined in a real application

    // Fetch and display actor data
    fetchActorData(actorId).then(actor => {
        actorNameElement.textContent = actor.name;

        actor.movies.forEach(movie => {
            const movieElement = document.createElement('div');
            movieElement.classList.add('movie');
            movieElement.innerHTML = `
                <img src="${movie.image}" alt="${movie.title}">
                <p>${movie.title}</p>
            `;
            moviesContainer.appendChild(movieElement);
        });
    });
});
