document.addEventListener('DOMContentLoaded', () => {
    // Redirigir a la sección guardada cuando se cargue la página
    const seccion = localStorage.getItem('seccion');
    if (seccion) {
        document.querySelector(`.nav-link[href="#${seccion}"]`)?.click();
    }

    // Guardar la sección seleccionada en localStorage al hacer clic en un enlace
    document.querySelectorAll('.nav-link').forEach(item => {
        item.addEventListener('click', () => {
            const href = item.getAttribute('href');
            if (href && href.startsWith('#')) {
                localStorage.setItem('seccion', href.substring(1)); // Guardar solo el ID sin el '#'
            }
        });
    });



    // Función toggle para mostrar/ocultar la contraseña
    function togglePassword(inputId, iconElement) {
        let passwordInput = document.getElementById(inputId);
        let eyeIcon = iconElement;

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }

    // Para los botones con data-toggle="password"
    document.querySelectorAll("[data-toggle='password']").forEach(function (element) {
        element.addEventListener("click", function () {
            togglePassword(this.dataset.target, this.querySelector("span"));
        });
    });

    // Para la función global que usa iconos específicos
    window.togglePassword = function (inputId, iconId) {
        togglePassword(inputId, document.getElementById(iconId));
    };
});
