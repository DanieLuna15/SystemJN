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
});
