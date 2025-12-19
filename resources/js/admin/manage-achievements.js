// Search Bar

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('achievementSearch');
    const tableRows = document.querySelectorAll('table tbody tr');
    let debounceTimer;

    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            // Clear previous timeout to debounce
            clearTimeout(debounceTimer);
            
            // Wait 0.3s after user stops typing before searching
            debounceTimer = setTimeout(() => {
                performSearch();
            }, 300);
        });
    }

    function performSearch() {
        const searchTerm = searchInput.value;
        const url = new URL(window.location);
        
        if(searchTerm.length > 0) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }
        
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('table tbody');
            const currentTableBody = document.querySelector('table tbody');
            
            if(newTableBody && currentTableBody) {
                currentTableBody.innerHTML = newTableBody.innerHTML;
            }
        })
        .catch(error => console.error('Search error:', error));
    }

    // Initialize delete button handlers with event delegation
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.btn-delete');
        if (deleteBtn) {
            e.preventDefault();
            const form = deleteBtn.closest('.delete-form');
            if (form) {
                const name = deleteBtn.dataset.deleteName;
                
                if (confirm(`Are you sure you want to delete "${name}"?`)) {
                    form.submit();
                }
            }
        }
    }, true);

    // ===== DIMENSIONS HANDLING =====
    
    const form = document.querySelector('form');
    
    // Hidden dimensions input should already exist in the HTML
    const dimensionsInput = document.getElementById('dimensions');

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            const metric = document.getElementById('metric').value;
            const dimensionsInputElem = document.getElementById('dimensions');
            
            // Update dimensions one last time before submission
            updateHiddenDimensionsInput();
            
            // Validate dimensions for metrics that require them
            if (['visits_by_continent', 'visits_by_activity', 'visits_by_type'].includes(metric)) {
                if (!dimensionsInputElem || !dimensionsInputElem.value) {
                    e.preventDefault();
                    alert('Please select dimensions for the ' + metric + ' metric');
                    return false;
                }
            }
        });
    }

    // Handle metric selection
    const metricSelect = document.getElementById('metric');
    const dimensionsContainer = document.getElementById('dimensions-container');
    const dimensionsInputs = document.getElementById('dimensions-inputs');

    if (metricSelect) {
        metricSelect.addEventListener('change', function() {
            updateDimensionsFields(this.value);
        });

        // Initialize on page load
        if (metricSelect.value) {
            updateDimensionsFields(metricSelect.value);
        }
    }

    function updateDimensionsFields(metric) {
        dimensionsInputs.innerHTML = '';

        // Auto-set aggregator based on metric
        const aggregatorSelect = document.getElementById('aggregator');
        if (metric === 'visits_by_continent') {
            aggregatorSelect.value = 'count_distinct';
        } else if (metric === 'visits_by_activity' || metric === 'visits_by_type') {
            aggregatorSelect.value = 'count';
        }

        if (metric === 'visits_by_continent') {
            dimensionsContainer.style.display = 'block';
            const continents = ['Asia', 'Europe', 'Africa', 'North America', 'South America', 'Australia'];
            const fieldset = document.createElement('fieldset');
            fieldset.style.border = 'none';
            fieldset.style.padding = '0';
            fieldset.style.margin = '0';
            
            const legend = document.createElement('legend');
            legend.textContent = 'Select continents:';
            legend.style.marginBottom = '10px';
            legend.style.fontWeight = 'bold';
            fieldset.appendChild(legend);

            continents.forEach(continent => {
                const label = document.createElement('label');
                label.style.display = 'block';
                label.style.marginBottom = '8px';
                
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'continent[]';
                checkbox.value = continent;
                checkbox.className = 'continent-checkbox';
                checkbox.style.marginRight = '6px';
                checkbox.style.outline = 'none';
                checkbox.style.boxShadow = 'none';
                checkbox.style.cursor = 'pointer';
                checkbox.addEventListener('change', updateHiddenDimensionsInput);
                
                label.appendChild(checkbox);
                label.appendChild(document.createTextNode(continent));
                fieldset.appendChild(label);
            });

            dimensionsInputs.appendChild(fieldset);

        } else if (metric === 'visits_by_activity') {
            dimensionsContainer.style.display = 'block';
            const activities = ['Active', 'Inactive', 'Extinct'];
            const fieldset = document.createElement('fieldset');
            fieldset.style.border = 'none';
            fieldset.style.padding = '0';
            fieldset.style.margin = '0';
            
            const legend = document.createElement('legend');
            legend.textContent = 'Select activity:';
            legend.style.marginBottom = '10px';
            legend.style.fontWeight = 'bold';
            fieldset.appendChild(legend);

            activities.forEach(activity => {
                const label = document.createElement('label');
                label.style.display = 'block';
                label.style.marginBottom = '8px';
                
                const radio = document.createElement('input');
                radio.type = 'radio';
                radio.name = 'activity_value';
                radio.value = activity;
                radio.className = 'activity-radio';
                radio.style.marginRight = '6px';
                radio.style.outline = 'none';
                radio.style.boxShadow = 'none';
                radio.style.cursor = 'pointer';
                radio.addEventListener('change', updateHiddenDimensionsInput);
                
                label.appendChild(radio);
                label.appendChild(document.createTextNode(activity));
                fieldset.appendChild(label);
            });

            dimensionsInputs.appendChild(fieldset);

        } else if (metric === 'visits_by_type') {
            dimensionsContainer.style.display = 'block';
            const typeInput = document.createElement('input');
            typeInput.type = 'text';
            typeInput.name = 'type_value';
            typeInput.className = 'form-input';
            typeInput.placeholder = 'Enter volcano type (e.g., Stratovolcano, Shield)';
            typeInput.addEventListener('input', updateHiddenDimensionsInput);
            dimensionsInputs.appendChild(typeInput);

        } else {
            dimensionsContainer.style.display = 'none';
        }
    }

    function updateHiddenDimensionsInput() {
        const hiddenInput = document.getElementById('dimensions');
        if (!hiddenInput) return;

        const continentCheckboxes = document.querySelectorAll('.continent-checkbox:checked');
        const activityRadio = document.querySelector('.activity-radio:checked');
        const typeInput = document.querySelector('input[name="type_value"]');

        let dimensionsObj = {};

        if (continentCheckboxes.length > 0) {
            dimensionsObj.continent = Array.from(continentCheckboxes).map(cb => cb.value);
        }

        if (activityRadio) {
            dimensionsObj.activity = activityRadio.value;
        }

        if (typeInput && typeInput.value) {
            dimensionsObj.type = typeInput.value;
        }

        const jsonValue = Object.keys(dimensionsObj).length > 0 ? JSON.stringify(dimensionsObj) : '';
        hiddenInput.value = jsonValue;
    }
});

