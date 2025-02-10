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
                <a href="javascript:void(0);" onclick="handleManageHolidayType(0)"
                    class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)">
                    <i class="mr-1 fa fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="HolidayTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="manageHoliday" tabindex="-1" aria-labelledby="manageHolidayLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="manageHolidayForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="form_title" class="modal-title">Manage Holiday</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date" class="form-control" required>
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

<div class="modal fade" id="deleteHoliday" tabindex="-1" aria-labelledby="deleteHolidayLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteHolidayForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Holiday</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this holiday?</p>
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
            const HolidayTable = $('#HolidayTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('hr.holiday.ajax_list') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'date', name: 'date' },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="handleManageHolidayType(${data})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="handleDeleteHoliday(${data})">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            window.handleManageHolidayType = function(id) {
                const modal = new bootstrap.Modal(document.getElementById('manageHoliday'));
                const form = $('#manageHolidayForm');
                const title = $('#form_title');
                const url = id
                    ? `{{ route('hr.holiday.manage_process', ':id') }}`.replace(':id', id)
                    : "{{ route('hr.holiday.manage_process') }}";

                if (id) {
                    $.get(`{{ url('hr/holiday/get') }}/${id}`, function(data) {
                        $('#name').val(data.name);
                        $('#date').val(data.date);
                        title.text("Edit holiday");
                    });
                } else {
                    form.trigger('reset');
                    title.text("Add New Holiday");
                }

                form.attr('action', url);
                modal.show();
            };

            window.handleDeleteHoliday = function(id) {
                const modal = new bootstrap.Modal(document.getElementById('deleteHoliday'));
                const form = $('#deleteHolidayForm');
                const url = `{{ route('hr.holiday.destroy', ':id') }}`.replace(':id', id);

                form.attr('action', url);
                modal.show();
            };

            $('#manageHolidayForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#manageHoliday').modal('hide');
                        HolidayTable.ajax.reload();
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

            $('#deleteHolidayForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#deleteHoliday').modal('hide');
                        HolidayTable.ajax.reload();
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
