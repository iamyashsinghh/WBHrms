@extends('admin.layouts.app')
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
                <a href="javascript:void(0);" onclick="handleManageDocumentType(0)"
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
                            <th>Name</th>
                            <th>Icon</th>
                            <th>Is Required</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="manageDocumentType" tabindex="-1" aria-labelledby="manageDocumentTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="manageDocumentTypeForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="form_title" class="modal-title">Manage Document Type</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="icon" class="form-label">Icon (CSS Class)</label>
                        <input type="text" id="icon" name="icon" class="form-control icon-picker">
                        <small class="text-muted">Enter a valid FontAwesome or other icon class (e.g., "fa fa-star").</small>
                    </div>
                    <div class="mb-3">
                        <label for="is_required" class="form-label">Is Required</label>
                        <select id="is_required" name="is_required" class="form-control" required>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
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

<div class="modal fade" id="deleteDocumentType" tabindex="-1" aria-labelledby="deleteDocumentTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteDocumentTypeForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Document Type</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this document type?</p>
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
                ajax: "{{ route('admin.document-type.ajax_list') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    {
                        data: 'icon',
                        name: 'icon',
                        render: function(data) {
                            return data ? `<i class="${data}"></i>` : '-';
                        }
                    },
                    {
                        data: 'is_required',
                        name: 'is_required',
                        render: function(data) {
                            return data == '1' ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="handleManageDocumentType(${data})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="handleDeleteDocumentType(${data})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            window.handleManageDocumentType = function(id) {
                const modal = new bootstrap.Modal(document.getElementById('manageDocumentType'));
                const form = $('#manageDocumentTypeForm');
                const title = $('#form_title');
                const url = id
                    ? `{{ route('admin.document-type.manage_process', ':id') }}`.replace(':id', id)
                    : "{{ route('admin.document-type.manage_process') }}";

                if (id) {
                    $.get(`{{ url('admin/document-type') }}/${id}`, function(data) {
                        $('#name').val(data.name);
                        $('#icon').val(data.icon);
                        $('#is_required').val(data.is_required);
                        title.text("Edit Document Type");
                    });
                } else {
                    form.trigger('reset');
                    title.text("Add New Document Type");
                }

                form.attr('action', url);
                modal.show();
            };

            window.handleDeleteDocumentType = function(id) {
                const modal = new bootstrap.Modal(document.getElementById('deleteDocumentType'));
                const form = $('#deleteDocumentTypeForm');
                const url = `{{ route('admin.document-type.destroy', ':id') }}`.replace(':id', id);

                form.attr('action', url);
                modal.show();
            };

            $('#manageDocumentTypeForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#manageDocumentType').modal('hide');
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

            $('#deleteDocumentTypeForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#deleteDocumentType').modal('hide');
                        documentTypeTable.ajax.reload();
                        toastr.success(response.success, 'Success');
                    },
                    error: function() {
                        toastr.error('An error occurred while deleting the document type.', 'Error');
                    }
                });
            });
        });
    </script>
@endsection
