<div id="trashModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmación</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form id="confirmationForm" action="" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="question"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        No
                    </button>
                    <button type="submit" class="btn btn-primary">Sí, confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.confirmationBtn', function() {
                var modal = $('#trashModal');
                let data = $(this).data();
                modal.find('.question').html(data.question);
                modal.find('#confirmationForm').attr('action', data.action);
                modal.modal('show');
            });

            $(document).on('click', '#trashModal .btn-secondary', function() {
                $('#trashModal').modal('hide');
            });
        })(jQuery);
    </script>
@endpush
