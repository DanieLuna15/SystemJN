document.addEventListener('DOMContentLoaded', () => {
    // Redirigir a la sección guardada cuando se cargue la página
    const seccion = localStorage.getItem('seccion');
    if (seccion) {
        document.querySelector(`.nav-link[data-section="${seccion}"]`)?.click();
    }

    // Guardar la sección seleccionada en localStorage al hacer clic en un enlace
    document.querySelectorAll('.nav-link').forEach(item => {
        item.addEventListener('click', () => {
            localStorage.setItem('seccion', item.getAttribute('data-section'));
        });
    });
});
