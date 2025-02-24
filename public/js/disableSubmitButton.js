// Para dehabilitar el botón de submit
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", function(event) {
            const submitButton = form.querySelector("button[type='submit']");
            if (submitButton && !submitButton.disabled) {
                // Obtener texto dinámico del atributo data-loading-text o usar un valor por defecto
                const loadingText = submitButton.getAttribute("data-loading-text") || "Procesando...";
                
                submitButton.disabled = true;
                submitButton.innerHTML = `<span class="fas fa-spinner fa-spin"></span> ${loadingText}`;
            }
        });
    });
});