document.addEventListener("DOMContentLoaded", function() {
    if (window.sweetAlertMessage) {
        let bgColor = "#fff";
        let textColor = "#fff";
        let iconHtml = "";

        switch (window.sweetAlertType) {
            case "success":
                bgColor = "#28a745"; 
                textColor = "#FFFFFF";
                iconHtml = '<i class="fas fa-check-circle"></i>';
                break;
            case "error":
                bgColor = "#dc3545"; 
                textColor = "#FFFFFF";
                iconHtml = '<i class="fas fa-times-circle"></i>';
                break;
            case "warning":
                bgColor = "#ffc107"; 
                textColor = "#FFFFFF";
                iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
                break;
            case "info":
                bgColor = "#17a2b8";
                textColor = "#FFFFFF";
                iconHtml = '<i class="fas fa-info-circle"></i>';
                break;
        }

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: window.sweetAlertType,
            html: `<span style="display: flex; align-items: center;">
                        ${iconHtml}
                        <span style="margin-left: 8px;"><strong>${window.sweetAlertMessage}</strong></span>
                   </span>`,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: bgColor,
            color: textColor,
            iconColor: textColor,
            customClass: {
                popup: 'swal2-toast-custom'
            }
        });
    }
});
