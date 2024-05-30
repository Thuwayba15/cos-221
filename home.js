const username = 'u19072211';
const password = 'University14';
const credentials = btoa(username + ':' + password);

document.addEventListener("DOMContentLoaded", function() {
    // Fetch all content on page load
    fetchAllContent();

    // Add event listener for search button
    document.getElementById("search-button").addEventListener("click", function() {
        const query = document.getElementById("search-input").value;
        searchContent(query);
    });

    // Add event listeners for sort options
    document.getElementById("sort-ratings").addEventListener("change", function() {
        const order = this.value;
        sortContent("sortByRating", order);
    });

    document.getElementById("sort-release").addEventListener("change", function() {
        const order = this.value;
        sortContent("sortByDate", order);
    });

    document.getElementById("sort-title").addEventListener("change", function() {
        const order = this.value;
        sortContent("sortByTitle", order);
    });
});

function fetchAllContent() {
    fetch('https://wheatley.cs.up.ac.za/u19072211/COS221/api.php', {
        method: 'POST',
        headers: { 
            'Authorization': 'Basic ' + credentials,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'getAllContent' })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            populateRecommendations(data.data);
        } else {
            console.error("Failed to fetch content:", data.data.message);
        }
    })
    .catch(error => console.error("Error fetching content:", error));
}

function searchContent(query) {
    fetch('https://wheatley.cs.up.ac.za/u19072211/COS221/api.php', {
        method: 'POST',
        headers: { 
            'Authorization': 'Basic ' + credentials,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: 'searchByTitle', title: query })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            populateRecommendations(data.data);
        } else {
            console.error("Failed to search content:", data.data.message);
        }
    })
    .catch(error => console.error("Error searching content:", error));
}

function sortContent(action, order) {
    fetch('https://wheatley.cs.up.ac.za/u19072211/COS221/api.php', {
        method: 'POST',
        headers: {
            'Authorization': 'Basic ' + credentials,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ action: action, order: order })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            populateRecommendations(data.data);
        } else {
            console.error("Failed to sort content:", data.data.message);
        }
    })
    .catch(error => console.error("Error sorting content:", error));
}

function populateRecommendations(content) {
    const container = document.querySelector('.recommendations-container');
    container.innerHTML = ''; // Clear existing content
    content.forEach(item => {
        const movieElement = document.createElement('div');
        movieElement.className = 'movie';
        movieElement.innerHTML = `
            <img src="${item.image}" alt="${item.title}">
            <p>${item.title}</p>
            <div class="button-container">
                <button class="view-more-button" data-movie-id="${item.id}">View More</button>
                <button class="watchlist-button" data-movie-id="${item.id}">Add to Watchlist</button>
                <button class="share-button">Share</button>
            </div>
        `;
        container.appendChild(movieElement);
    });
}
