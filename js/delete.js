document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('search-button');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const deleteContainer = document.getElementById('delete-container');
    const itemDetails = document.getElementById('item-details');
    const deleteButton = document.getElementById('delete-button');
    let selectedItemId = null;

    searchButton.addEventListener('click', function() {
        const query = searchInput.value.trim();
        if (query) {
            // Placeholder: Fetch search results from the server
            fetchSearchResults(query);
        }
    });

    searchResults.addEventListener('click', function(event) {
        if (event.target && event.target.matches('div.result-item')) {
            const itemId = event.target.dataset.id;
            // Placeholder: Fetch item details from the server
            fetchItemDetails(itemId);
        }
    });

    deleteButton.addEventListener('click', function() {
        if (selectedItemId) {
            // Placeholder: Delete item from the server
            deleteItem(selectedItemId);
        }
    });

    function fetchSearchResults(query) {
        // This should be replaced with actual server call
        // Simulated search results
        const results = [
            { id: 1, title: 'Fast and Furious 1' },
            { id: 2, title: 'Fast and Furious 2' },
        ];

        searchResults.innerHTML = results.map(result => `
            <div class="result-item" data-id="${result.id}">
                ${result.title}
            </div>
        `).join('');
    }

    function fetchItemDetails(itemId) {
        // This should be replaced with actual server call
        // Simulated item details
        const item = {
            id: itemId,
            type: 'movie',
            language: 'English',
            genre: 'Action',
            title: 'Fast and Furious 1',
            releaseDate: '2001-06-22',
            IMDBrating: 6.8,
            length: 106
        };

        selectedItemId = item.id;
        itemDetails.textContent = `
            Type: ${item.type}
            Language: ${item.language}
            Genre: ${item.genre}
            Title: ${item.title}
            Release Date: ${item.releaseDate}
            IMDB Rating: ${item.IMDBrating}
            ${item.type === 'movie' ? `Length: ${item.length} minutes` : ''}
        `;
        deleteContainer.style.display = 'block';
    }

    function deleteItem(itemId) {
        // This should be replaced with actual server call to delete item
        console.log('Deleting item with id:', itemId);
        alert('Item deleted successfully!');
        deleteContainer.style.display = 'none';
        searchResults.innerHTML = '';
        searchInput.value = '';
    }
});
