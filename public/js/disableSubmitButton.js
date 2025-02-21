document.addEventListener('DOMContentLoaded', function() {
    var submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(function(submitButton) {
        submitButton.addEventListener('click', function(event) {
            event.preventDefault(); // Evita el envío inmediato del formulario
            if (submitButton.disabled) {
                // Si el botón ya está deshabilitado, no hacer nada.
                return;
            }
            submitButton.disabled = true;
            submitButton.innerHTML = 'Enviando...';

            // Envía el formulario manualmente
            submitButton.closest('form').submit();
        });
    });
});