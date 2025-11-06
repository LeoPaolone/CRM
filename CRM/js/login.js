document.addEventListener('DOMContentLoaded', function () {

    // Selecciona el botón para mostrar/ocultar la contraseña
    const toggleButton = document.querySelector('input[name="password"]').closest('.relative').querySelector('button');

    if (toggleButton) {
        toggleButton.addEventListener('click', function () {
            const inputField = this.closest('.relative').querySelector('input');
            const icon = this.querySelector('.material-symbols-outlined');
            
            if (inputField.type === 'password') {
                inputField.type = 'text';
                icon.textContent = 'visibility_off'; 
            } else {
                inputField.type = 'password';
                icon.textContent = 'visibility'; 
            }
        });
    }
});



