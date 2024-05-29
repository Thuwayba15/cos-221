document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const lengthGroup = document.getElementById('length-group');
    const seasonsGroup = document.getElementById('seasons-group');

    typeSelect.addEventListener('change', function() {
        if (typeSelect.value === 'movie') {
            lengthGroup.style.display = 'block';
            seasonsGroup.style.display = 'none';
        } else if (typeSelect.value === 'tv-series') {
            lengthGroup.style.display = 'none';
            seasonsGroup.style.display = 'block';
        }
    });

    // Optional: Trigger change event on page load to set initial visibility
    typeSelect.dispatchEvent(new Event('change'));
});
