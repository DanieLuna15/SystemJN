document.addEventListener("DOMContentLoaded", function () {
    if (window.sweetAlertMessage) {
        let bgColor = '#333';  // Color de fondo por defecto para el toast
        let textColor = '#fff'; // Texto blanco por defecto
        let iconColor = '#fff'; // Icono blanco por defecto

        // Lógica para asignar colores según el tipo de mensaje
        switch (window.sweetAlertType) {
            case "success":
                bgColor = "#28a745";  // Verde para éxito
                iconColor = "#fff";   // Icono blanco
                break;
            case "error":
                bgColor = "#dc3545";  // Rojo para error
                iconColor = "#fff";   // Icono blanco
                break;
            case "warning":
                bgColor = "#ffc107";  // Amarillo para advertencias
                iconColor = "#000";   // Icono negro
                break;
            case "info":
                bgColor = "#17a2b8";  // Azul para información
                iconColor = "#fff";   // Icono blanco
                break;
            default:
                bgColor = '#333';     // Fondo oscuro predeterminado
                iconColor = '#fff';   // Icono blanco por defecto
        }

        // Mostrar el toast con los colores correspondientes y el tiempo de 3 segundos
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: window.sweetAlertType,
            title: window.sweetAlertMessage,
            showConfirmButton: false,
            timer: 3000,             // Mantener el toast durante 3 segundos
            timerProgressBar: true,
            background: bgColor,    // Fondo dinámico según tipo
            color: textColor,        // Texto blanco
            iconColor: iconColor,    // Icono con color dinámico
            stopOnHover: true,      // Evitar que el toast se cierre al hacer hover
        });
    }
});
