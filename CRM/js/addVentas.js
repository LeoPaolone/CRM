document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const messageDiv = document.createElement('div');

    // Ensure the form exists before proceeding
    if (!form) {
        console.error('No se encontró el formulario en la página.');
        return;
    }

    // Insert the message container at the beginning of the form
    messageDiv.className = 'p-4 mb-4 text-white rounded-lg hidden';
    form.insertBefore(messageDiv, form.firstChild);

    // Function to display status messages (success or error)
    function showMessage(message, className) {
        messageDiv.textContent = message;
        messageDiv.className = `p-4 mb-4 text-white rounded-lg ${className}`;
        messageDiv.classList.remove('hidden');
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent traditional form submission

        // Hide previous messages
        messageDiv.classList.add('hidden');

        // Create a FormData object to collect form data
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // --- CLIENT-SIDE VALIDATION ---

        // 1. Validate empty fields
        if (!data.client_id || !data.product_name || !data.quantity || !data.price || !data.sale_date) {
            showMessage('Por favor, completa todos los campos obligatorios.', 'bg-red-500');
            return;
        }

        // 2. Validate quantity is a positive number
        if (isNaN(data.quantity) || parseInt(data.quantity) <= 0) {
            showMessage('La cantidad debe ser un número positivo.', 'bg-red-500');
            return;
        }

        // 3. Validate price is a positive number
        if (isNaN(data.price) || parseFloat(data.price) <= 0) {
            showMessage('El precio debe ser un número positivo.', 'bg-red-500');
            return;
        }

    });
});