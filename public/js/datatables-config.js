document.addEventListener("DOMContentLoaded", function () {
    let tables = document.querySelectorAll(".datatable");
    tables.forEach(function (table) {
        $(table).DataTable({
            "pageLength": 10,
            "lengthChange": true,
            "language": {
                "emptyTable": "No hay información disponible en esta tabla",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "lengthMenu": "Mostrar _MENU_ registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true,
            "autoWidth": false,
            "dom": 'lBfrtip',  // 🔹 Agregado 'l' para mostrar el select
            "buttons": [
                {
                    text: '<i class="fa fa-file-excel"></i> Exportar a Excel',
                    extend: 'excelHtml5',
                    className: 'btn btn-success'
                },
                {
                    text: '<i class="fa fa-file-pdf"></i> Exportar a PDF',
                    extend: 'pdfHtml5',
                    className: 'btn btn-danger'
                },
                {
                    text: '<i class="fa fa-file-csv"></i> Exportar a CSV',
                    extend: 'csvHtml5',
                    className: 'btn btn-info'
                },
                // {
                //     text: '<i class="fa fa-copy"></i> Copiar',
                //     extend: 'copyHtml5',
                //     className: 'btn btn-secondary'
                // },
                {
                    text: '<i class="fa fa-print"></i> Imprimir',
                    extend: 'print',
                    className: 'btn btn-warning'
                }
            ]
        }).buttons().container().appendTo($(table).closest(".dataTables_wrapper").find('.row:eq(0)'));
    });
});

