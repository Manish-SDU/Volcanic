document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('volcanoSearch');
    let debounceTimer;

    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            // Clear previous timeout to debounce
            clearTimeout(debounceTimer);
            
            // Wait 0.3s after user stops typing before searching -> Optimizes performance
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
    // Use document-level delegation to avoid duplicate listeners
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.btn-delete');
        if (deleteBtn) {
            e.preventDefault();
            const form = deleteBtn.closest('.delete-form');
            if (form) {
                const volcanoName = deleteBtn.dataset.deleteName;
                const deleteType = deleteBtn.dataset.deleteType;
                
                if (confirm(`Are you sure you want to delete "${volcanoName}"?`)) {
                    form.submit();
                }
            }
        }
    }, true);
});