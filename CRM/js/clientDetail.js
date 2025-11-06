document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const deleteButton = document.querySelector('button[name="delete_client"]');

    // No necesitamos un listener para el botón de actualizar, ya que el formulario lo maneja nativamente.

    // Manejar la eliminación del cliente desde JS para una mejor experiencia
    if (form && deleteButton) {
        // El botón de eliminar es de tipo 'submit', así que escuchamos el evento 'submit' del formulario.
        form.addEventListener('submit', function (event) {
            // Verificamos si el botón presionado fue el de eliminar.
            // 'submitter' es el botón que envió el formulario.
            if (event.submitter && event.submitter.name === 'delete_client') {
                
                // Prevenimos el envío inmediato del formulario.
                event.preventDefault();

                // Mostramos una confirmación al usuario.
                const isConfirmed = confirm('¿Estás seguro de que quieres eliminar este cliente? Esta acción no se puede deshacer.');

                // Si el usuario confirma, enviamos el formulario.
                if (isConfirmed) {
                    // Para enviar el formulario con el botón de eliminar, podemos crear un input oculto
                    // y luego enviar el formulario programáticamente.
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'delete_client';
                    hiddenInput.value = '1';
                    form.appendChild(hiddenInput);
                    form.submit();
                }
            }
        });
    }
});