document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const lengthGroup = document.getElementById('length-group');
    const seasonsGroup = document.getElementById('seasons-group');

    const awardsGroup = document.getElementById('awards-group');
    const ageRatingGroup = document.getElementById('ageRating-group');
    const boxOfficeGroup = document.getElementById('boxOffice-group');
    const imageGroup = document.getElementById('image-group');
    const productionStudioGroup = document.getElementById('productionStudio-group');
    const summaryGroup = document.getElementById('summary-group');
    const statusGroup = document.getElementById('status-group');

    function updateFormFields() {
        const type = typeSelect.value;
        if (type === 'movie') {
            lengthGroup.style.display = '';
            seasonsGroup.style.display = 'none';
            awardsGroup.style.display = '';
            ageRatingGroup.style.display = '';
            boxOfficeGroup.style.display = '';
            imageGroup.style.display = '';
            productionStudioGroup.style.display = '';
            summaryGroup.style.display = '';
            statusGroup.style.display = 'none';
        } else if (type === 'tv-series') {
            lengthGroup.style.display = 'none';
            seasonsGroup.style.display = '';
            awardsGroup.style.display = 'none';
            ageRatingGroup.style.display = 'none';
            boxOfficeGroup.style.display = 'none';
            imageGroup.style.display = '';
            productionStudioGroup.style.display = '';
            summaryGroup.style.display = '';
            statusGroup.style.display = '';
        }
    }

    typeSelect.addEventListener('change', updateFormFields);
    updateFormFields(); // Initial call to set the correct state on page load

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
