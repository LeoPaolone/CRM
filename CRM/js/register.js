
document.addEventListener('DOMContentLoaded', function () {

    // Corregido: Seleccionar los botones que contienen el ícono, no solo el ícono.
    const toggleButtons = document.querySelectorAll('.relative button');

    // Add click event listeners to each toggle button
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            
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
    });

    
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
    const form = document.querySelector('form');
    const messageDiv = document.createElement('div'); 
    messageDiv.className = 'p-4 text-white rounded-lg hidden';
    confirmPasswordInput.parentElement.parentElement.appendChild(messageDiv); 

    confirmPasswordInput.addEventListener('input', function () {
        if (passwordInput.value !== confirmPasswordInput.value) {
            messageDiv.className = 'p-4 text-white rounded-lg bg-red-500';
            messageDiv.textContent = 'Las contraseñas no coinciden';
            messageDiv.classList.remove('hidden');
        } else {
            messageDiv.className = 'p-4 text-white rounded-lg bg-green-500';
            messageDiv.textContent = 'Las contraseñas coinciden';
            messageDiv.classList.remove('hidden');
        }
    });

   
    passwordInput.addEventListener('input', function () {
        if (confirmPasswordInput.value) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                messageDiv.className = 'p-4 text-white rounded-lg bg-red-500';
                messageDiv.textContent = 'Las contraseñas no coinciden';
                messageDiv.classList.remove('hidden');
            } else {
                messageDiv.className = 'p-4 text-white rounded-lg bg-green-500';
                messageDiv.textContent = 'Las contraseñas coinciden';
                messageDiv.classList.remove('hidden');
            }
        } else {
            messageDiv.classList.add('hidden');
        }
    });

 
    form.addEventListener('submit', function (event) {
        if (passwordInput.value !== confirmPasswordInput.value) {
            event.preventDefault(); 
            messageDiv.className = 'p-4 text-white rounded-lg bg-red-500';
            messageDiv.textContent = 'Por favor, asegúrate de que las contraseñas coincidan';
            messageDiv.classList.remove('hidden');
        }
    });
});