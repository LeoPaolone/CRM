/**
 * CRM Client Management
 * Main JavaScript file for client list functionality
 */

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const searchInput = document.querySelector('input[placeholder="Buscar clientes..."]');
    const clientList = document.querySelector('.flex.flex-col.gap-3');
    const filterButton = document.getElementById('filter-clients-button');
    
    // State
    let currentSort = 'name_asc';
    let activeDropdown = null;

    // Initialize the application
    function init() {
        if (!searchInput || !clientList) {
            console.error('Required elements not found in the DOM');
            return;
        }

        setupEventListeners();
    }

    // Set up all event listeners
    function setupEventListeners() {
        // Search functionality
        searchInput.addEventListener('input', handleSearch);
        
        // Filter button
        if (filterButton) {
            filterButton.addEventListener('click', handleFilterButtonClick);
        }
        
        // Client list interactions
        clientList.addEventListener('click', handleClientListClick);
    }

    // Handle search input
    function handleSearch(event) {
        const searchTerm = event.target.value.trim().toLowerCase();
        const clientItems = clientList.querySelectorAll('.client-item');
        let hasVisibleItems = false;

        clientItems.forEach(item => {
            const name = item.querySelector('.client-name')?.textContent?.toLowerCase() || '';
            const company = item.querySelector('.client-company')?.textContent?.toLowerCase() || '';
            const isVisible = name.includes(searchTerm) || company.includes(searchTerm);
            
            item.classList.toggle('hidden', !isVisible);
            if (isVisible) hasVisibleItems = true;
        });

        updateNoResultsMessage(hasVisibleItems);
    }

    // Update no results message
    function updateNoResultsMessage(hasVisibleItems) {
        let message = clientList.querySelector('.no-results');
        
        if (!hasVisibleItems && !message) {
            message = document.createElement('div');
            message.className = 'no-results p-4 text-sm text-slate-500 dark:text-slate-400 text-center';
            message.textContent = 'No se encontraron clientes.';
            clientList.appendChild(message);
        } else if (hasVisibleItems && message) {
            message.remove();
        }
    }

    // Handle filter button click
    function handleFilterButtonClick(event) {
        event.stopPropagation();
        
        // Remove existing filter menu if open
        const existingMenu = document.querySelector('.filter-menu');
        if (existingMenu) {
            existingMenu.remove();
            return;
        }

        // Create and show filter menu
        const filterMenu = document.createElement('div');
        filterMenu.className = 'absolute right-0 mt-2 w-48 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg p-4 filter-menu z-30';
        filterMenu.innerHTML = `
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Ordenar por:</p>
            <ul class="mt-2 space-y-2">
                <li><button class="w-full text-left text-sm p-2 rounded ${currentSort === 'name_asc' ? 'bg-blue-100 dark:bg-blue-900/50' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'}" data-sort="name_asc">Nombre (A-Z)</button></li>
                <li><button class="w-full text-left text-sm p-2 rounded ${currentSort === 'name_desc' ? 'bg-blue-100 dark:bg-blue-900/50' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'}" data-sort="name_desc">Nombre (Z-A)</button></li>
                <li><button class="w-full text-left text-sm p-2 rounded ${currentSort === 'company_asc' ? 'bg-blue-100 dark:bg-blue-900/50' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800'}" data-sort="company_asc">Empresa (A-Z)</button></li>
            </ul>
        `;

        // Add sort event listeners
        filterMenu.querySelectorAll('[data-sort]').forEach(button => {
            button.addEventListener('click', () => {
                currentSort = button.dataset.sort;
                sortClients(currentSort);
                filterMenu.remove();
            });
        });

        // Add to DOM
        filterButton.appendChild(filterMenu);

        // Close on outside click
        const closeMenu = (e) => {
            if (!filterMenu.contains(e.target) && e.target !== filterButton) {
                filterMenu.remove();
                document.removeEventListener('click', closeMenu);
            }
        };
        
        setTimeout(() => document.addEventListener('click', closeMenu));
    }

    // Sort clients
    function sortClients(sortType) {
        const clientItems = Array.from(clientList.querySelectorAll('.client-item:not(.hidden)'));
        
        clientItems.sort((a, b) => {
            const nameA = a.querySelector('.client-name')?.textContent?.toLowerCase() || '';
            const nameB = b.querySelector('.client-name')?.textContent?.toLowerCase() || '';
            const companyA = a.querySelector('.client-company')?.textContent?.toLowerCase() || '';
            const companyB = b.querySelector('.client-company')?.textContent?.toLowerCase() || '';

            switch (sortType) {
                case 'name_asc': return nameA.localeCompare(nameB);
                case 'name_desc': return nameB.localeCompare(nameA);
                case 'company_asc': return companyA.localeCompare(companyB);
                default: return 0;
            }
        });

        // Re-append sorted items
        const fragment = document.createDocumentFragment();
        clientItems.forEach(item => fragment.appendChild(item));
        clientList.appendChild(fragment);
    }

    // Handle client list interactions
    function handleClientListClick(event) {
        const moreButton = event.target.closest('.more-options-button');
        if (!moreButton) return;

        event.stopPropagation();
        
        // Close existing dropdown if open
        if (activeDropdown) {
            activeDropdown.remove();
            if (activeDropdown === moreButton) {
                activeDropdown = null;
                return;
            }
        }

        const clientItem = moreButton.closest('.client-item');
        const clientName = clientItem.querySelector('.client-name')?.textContent || 'Cliente';
        const clientId = clientItem.dataset.clientId;

        if (!clientId) {
            console.error('Client ID not found');
            return;
        }

        // Create dropdown menu
        const dropdown = document.createElement('div');
        dropdown.className = 'absolute right-0 mt-2 w-48 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-lg p-4 dropdown-menu z-30';
        dropdown.innerHTML = `
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Opciones para ${escapeHtml(clientName)}</p>
            <ul class="mt-2 space-y-2">
                <li><button class="w-full text-left text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 p-2 rounded view-details" data-client-id="${escapeHtml(clientId)}">Ver detalles</button></li>
                <li><button class="w-full text-left text-sm text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 p-2 rounded edit-client" data-client-id="${escapeHtml(clientId)}">Editar</button></li>
                <li><button class="w-full text-left text-sm text-red-600 dark:text-red-500 hover:bg-red-100 dark:hover:bg-red-900/50 p-2 rounded delete-client" data-client-id="${escapeHtml(clientId)}">Eliminar</button></li>
            </ul>
        `;

        // Position and show dropdown
        moreButton.appendChild(dropdown);
        activeDropdown = moreButton;

        // Add dropdown actions
        setupDropdownActions(dropdown, clientId, clientName);

        // Close on outside click
        const closeDropdown = (e) => {
            if (!dropdown.contains(e.target) && e.target !== moreButton) {
                dropdown.remove();
                if (activeDropdown === moreButton) {
                    activeDropdown = null;
                }
                document.removeEventListener('click', closeDropdown);
            }
        };
        
        setTimeout(() => document.addEventListener('click', closeDropdown));
    }

    // Setup dropdown menu actions
    function setupDropdownActions(dropdown, clientId, clientName) {
        // View details
        const viewBtn = dropdown.querySelector('.view-details');
        if (viewBtn) {
            viewBtn.addEventListener('click', () => {
                window.location.href = `clientDetail.php?id=${encodeURIComponent(clientId)}`;
            });
        }

        // Edit client
        const editBtn = dropdown.querySelector('.edit-client');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                window.location.href = `editClient.php?id=${encodeURIComponent(clientId)}`;
            });
        }

        // Delete client
        const deleteBtn = dropdown.querySelector('.delete-client');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                if (confirm(`¿Estás seguro de que quieres eliminar a ${clientName}?`)) {
                    deleteClient(clientId, clientName);
                }
            });
        }
    }

    // Delete client function
    async function deleteClient(clientId, clientName) {
        try {
            const response = await fetch(`/api/delete_client.php?id=${encodeURIComponent(clientId)}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const result = await response.json();
            
            if (result.success) {
                // Remove client from DOM
                const clientItem = document.querySelector(`.client-item[data-client-id="${escapeHtml(clientId)}"]`);
                if (clientItem) {
                    clientItem.remove();
                }
                // Show success message
                showNotification(`Cliente "${clientName}" eliminado correctamente`, 'success');
            } else {
                throw new Error(result.message || 'Error al eliminar el cliente');
            }
        } catch (error) {
            console.error('Error deleting client:', error);
            showNotification(error.message || 'Error al eliminar el cliente', 'error');
        }
    }

    // Show notification
    function showNotification(message, type = 'info') {
        // You can implement a proper notification system here
        alert(message);
    }

    // Simple HTML escape function
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Start the application
    init();
});
