document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.getElementById('search-button');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    const updateForm = document.getElementById('update-form');
    const typeSelect = document.getElementById('type');
    const lengthGroup = document.getElementById('length-group');
    const seasonsGroup = document.getElementById('seasons-group');

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

    typeSelect.addEventListener('change', function() {
        toggleFieldsBasedOnType(typeSelect.value);
    });

    updateForm.addEventListener('submit', function(event) {
        event.preventDefault();
        // Placeholder: Update item details on the server
        updateItemDetails(new FormData(updateForm));
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
            type: 'movie',
            language: 'English',
            genre: 'Action',
            title: 'Fast and Furious 1',
            releaseDate: '2001-06-22',
            IMDBrating: 6.8,
            length: 106,
            seasons: null
        };

        document.getElementById('type').value = item.type;
        document.getElementById('language').value = item.language;
        document.getElementById('genre').value = item.genre;
        document.getElementById('title').value = item.title;
        document.getElementById('releaseDate').value = item.releaseDate;
        document.getElementById('IMDBrating').value = item.IMDBrating;
        if (item.type === 'movie') {
            document.getElementById('length').value = item.length;
        } else {
            document.getElementById('seasons').value = item.seasons;
        }

        toggleFieldsBasedOnType(item.type);
        updateForm.style.display = 'block';
    }

    function toggleFieldsBasedOnType(type) {
        if (type === 'movie') {
            lengthGroup.style.display = 'block';
            seasonsGroup.style.display = 'none';
        } else if (type === 'tv-series') {
            lengthGroup.style.display = 'none';
            seasonsGroup.style.display = 'block';
        }
    }

    function updateItemDetails(formData) {
        // This should be replaced with actual server call to update item details
        console.log('Updating item with data:', Object.fromEntries(formData.entries()));
        alert('Item updated successfully!');
        updateForm.reset();
        updateForm.style.display = 'none';
        searchResults.innerHTML = '';
    }
});
