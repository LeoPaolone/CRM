document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const messageDiv = document.createElement('div');
    
    // Asegurarse de que el formulario exista antes de continuar
    if (!form) {
        console.error('No se encontró el formulario en la página.');
        return;
    }

    // Insertar el contenedor de mensajes al principio del formulario
    messageDiv.className = 'p-4 mb-4 text-white rounded-lg hidden';
    form.insertBefore(messageDiv, form.firstChild);

    // Función para mostrar mensajes de estado (éxito o error)
    function showMessage(message, className) {
        messageDiv.textContent = message;
        messageDiv.className = `p-4 mb-4 text-white rounded-lg ${className}`;
        messageDiv.classList.remove('hidden');
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevenir el envío tradicional del formulario

        // Ocultar mensajes anteriores
        messageDiv.classList.add('hidden');

        // Crear un objeto FormData para recopilar los datos del formulario
        const formData = new FormData(form);
        const formObject = Object.fromEntries(formData.entries());

        // --- VALIDACIÓN DEL LADO DEL CLIENTE ---

        // 1. Validar campos vacíos
        if (!formObject.name || !formObject.email || !formObject.phone || !formObject.company || !formObject.address) {
            showMessage('Por favor, completa todos los campos obligatorios.', 'bg-red-500');
            return;
        }

        // 2. Validar formato de correo electrónico
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(formObject.email)) {
            showMessage('Por favor, introduce un correo electrónico válido.', 'bg-red-500');
            return;
        }

        // 3. Validar formato de teléfono (ejemplo simple)
        const phonePattern = /^[0-9]{7,15}$/; // Permite de 7 a 15 dígitos
        if (!phonePattern.test(formObject.phone)) {
            showMessage('Por favor, introduce un número de teléfono válido (solo dígitos).', 'bg-red-500');
            return;
        }

        // --- ENVÍO DE DATOS (AJAX con Fetch) ---

        fetch('../api/add_client_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formObject),
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showMessage('Cliente agregado exitosamente!', 'bg-green-500');
                form.reset(); // Limpiar el formulario
            } else {
                showMessage('Error: ' + result.message, 'bg-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la solicitud fetch:', error);
            showMessage('Ocurrió un error al conectar con el servidor. Inténtalo de nuevo.', 'bg-red-500');
        });

    });
});