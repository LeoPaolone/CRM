document.addEventListener('DOMContentLoaded', function () {
    const searchButton = document.getElementById('search-button');
    const searchBarContainer = document.getElementById('search-bar-container');

    if (searchButton && searchBarContainer) {
        searchButton.addEventListener('click', function () {
            searchBarContainer.classList.toggle('hidden');
            // Optionally focus the input when it appears
            if (!searchBarContainer.classList.contains('hidden')) {
                searchBarContainer.querySelector('input[name="search_query"]').focus();
            }
        });
    }

    // Add task button (placeholder for form or redirect)
    const addTaskButton = document.querySelector('.absolute.bottom-24.right-4');
    if (addTaskButton) {
        addTaskButton.addEventListener('click', function () {
            // Placeholder: Redirect to add task page or show modal
            alert('Abrir formulario para agregar nueva tarea');
            // Example: window.location.href = 'addTask.php';
        });
    }
});