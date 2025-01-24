@extends('hr.layouts.app')
@section('title', $page_heading)
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection

@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $page_heading }}</h1>
                </div>
            </div>
            <div class="my-4 button-group">
                <a href="javascript:void(0);" onclick="handleManageNotificationType(0)"
                    class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)">
                    <i class="mr-1 fa fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="documentTypeTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Value</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="manageNotification" tabindex="-1" aria-labelledby="manageNotificationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="manageNotificationForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="form_title" class="modal-title">Manage Document Type</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="value" class="form-label">Value</label>
                        <input type="text" id="value" name="value" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteNotification" tabindex="-1" aria-labelledby="deleteNotificationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteNotificationForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Notification</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this Notification ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            const documentTypeTable = $('#documentTypeTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('hr.notification.ajax_list') }}",
    columns: [
        { data: 'id', name: 'id' },
        { data: 'value', name: 'value' },
        {
            data: 'id',
            name: 'actions',
            orderable: false,
            searchable: false,
            className: 'text-center',
            render: function(data, type, row) {
                return `
                    <button class="btn btn-sm btn-primary" onclick="handleManageNotificationType(${data}, '${row.value}')">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="handleDeleteNotificationType(${data})">
                        <i class="fa fa-trash"></i>
                    </button>
                `;
            }
        }
    ]
});


            window.handleManageNotificationType = function(id , value) {
                const modal = new bootstrap.Modal(document.getElementById('manageNotification'));
                const form = $('#manageNotificationForm');
                const title = $('#form_title');
                const url = id
                    ? `{{ route('hr.notification.manage_process', ':id') }}`.replace(':id', id)
                    : "{{ route('hr.notification.manage_process') }}";
                if (id) {
                        $('#value').val(value);
                        title.text("Edit Notification");
                } else {
                    form.trigger('reset');
                    title.text("Add New Notification");
                }
                form.attr('action', url);
                modal.show();
            };

            window.handleDeleteNotificationType = function(id) {
                const modal = new bootstrap.Modal(document.getElementById('deleteNotification'));
                const form = $('#deleteNotificationForm');
                const url = `{{ route('hr.notification.destroy', ':id') }}`.replace(':id', id);

                form.attr('action', url);
                modal.show();
            };

            $('#manageNotificationForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#manageNotification').modal('hide');
                        documentTypeTable.ajax.reload();
                        toastr.success(response.success, 'Success');
                    },
                    error: function(error) {
                        const errors = error.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0], 'Error');
                        }
                    }
                });
            });

            $('#deleteNotificationForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#deleteNotification').modal('hide');
                        documentTypeTable.ajax.reload();
                        toastr.success(response.success, 'Success');
                    },
                    error: function() {
                        toastr.error('An error occurred while deleting the Notification.', 'Error');
                    }
                });
            });
        });
    </script>
@endsection
