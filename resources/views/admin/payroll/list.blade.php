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
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="d-flex justify-content-end">
                    <form action="javascript:void(0);" id="filter-form" class="form-inline">
                        <div class="mb-2 form-group">
                            <select name="employee" id="employee" class="form-control">
                                <option value="all">All</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->emp_code }}">{{ $user->name }}--{{ $user->emp_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2 form-group mx-sm-1">
                            <select name="month" id="month" class="form-control">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}">
                                        {{ \Carbon\Carbon::createFromDate(null, $m)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2 form-group">
                            <select name="year" id="year" class="form-control">
                                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <button type="button" id="filter-button" class="mb-2 btn btn-primary mx-sm-1"
                            style="background-color: #891010; border: none;">
                            <i id="loading-icon" class="fa fa-spinner fa-spin" style="display: none;"></i> Generate Payslips
                        </button>
                    </form>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="table-responsive">
                <table id="documentTypeTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Emp Code</th>
                            <th>Name</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Status</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const documentTypeTable = $('#documentTypeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.payroll.ajax_list') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'emp_code',
                        name: 'emp_code'
                    },
                    {
                        data: 'employee.name',
                        name: 'employee.name'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return moment.utc(data).utcOffset(330).format('DD MMM YYYY HH:mm a');
                        }
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        render: function(data) {
                            return moment.utc(data).utcOffset(330).format('DD MMM YYYY HH:mm a');
                        }
                    },
                    {
                        data: 'is_paid',
                        name: 'is_paid',
                        render: function(data) {
                            if (data === 1) {
                                return `<div class="badge badge-success">Paid</div>`;
                            } else {
                                return `<div class="badge badge-danger">Unpaid</div>`;
                            }
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'd-flex justify-content-center',
                        render: function(data, type, row) {
                            if (row.is_paid == 1) {
                                return `<div class="d-flex">
                                <a href='#' class="mx-2 badge badge-warning" title="View"><i class="p-2 fa-solid fa-file"></i></a>
                                <a href='#' class="badge badge-danger" title="Delete"><i class="p-2 fa-solid fa-trash"></i></a>
                                </div>`;
                            } else {
                                return `<div class="d-flex">
                                <a href='#' class="badge badge-warning" title="View"><i class="p-2 fa-solid fa-file"></i></a>
                                <a href='#' class="mx-2 badge badge-success" title="Mark as paid"><i class="p-2 fa-solid fa-indian-rupee-sign"></i></a>
                                <a href='#' class="badge badge-danger" title="Delete"><i class="p-2 fa-solid fa-trash"></i></a>
                                </div>`;
                            }
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
            })
        })

        document.getElementById('filter-button').addEventListener('click', function() {
            const button = this;
            const loadingIcon = document.getElementById('loading-icon');
            const employee = document.getElementById('employee').value;
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;

            button.disabled = true;
            loadingIcon.style.display = 'inline-block';

            fetch(`{{ route('admin.payroll.generate') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        employee,
                        month,
                        year
                    })
                }).then(response => {
                    return response.json();
                })
                .then(data => {
                    if (data?.success) {
                        $('#documentTypeTable').DataTable().ajax.reload(null, false);
                        toastr.success(data.message || 'Payslips generated successfully!');
                    } else {
                        toastr.error(data.message || 'An error occurred while generating payslips.');
                    }
                })
                .catch(error => {
                    toastr.error('An unexpected error occurred.');
                })
                .finally(() => {
                    button.disabled = false;
                    loadingIcon.style.display = 'none';
                });
        });
    </script>
@endsection
