@extends('hr.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading )
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
                    <a href="{{ route('hr.employee.manage') }}" class="btn btn-sm text-light buttons-print"
                        style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</a>
                </div>
                <div class="my-4 button-group vendor-categories">
                    @foreach ($roles as $role)
                        <button class="btn btn-secondary btn-sm filter-btn"
                            data-role-id="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</button>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive">
                    <table id="serverTable" class="table text-sm">
                        <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                            <tr>
                                <th class="text-nowrap">ID</th>
                                <th class="text-nowrap">Profile Image</th>
                                <th class="text-nowrap">Emp Code</th>
                                <th class="text-nowrap">Name</th>
                                <th class="text-nowrap">Phone</th>
                                <th class="text-nowrap">Employee Designation</th>
                                <th class="text-nowrap">Role</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Is Active</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#serverTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('hr.employee.ajax_list') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false,
                    },
                    {
                        data: 'profile_image',
                        name: 'profile_image',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<a onclick="handle_view_image('${data}', '{{ route('hr.employee.update_profile_image') }}/${row.emp_code}')" href="javascript:void(0);"><img class="img-thumbnail" src="${data}" style="width: 50px;" onerror="this.onerror=null; this.src='{{ asset('images/default-user.png') }}'"></a>`;
                        }
                    },
                    {
                        data: 'emp_code',
                        name: 'emp_code',
                        render: function (data, type, row){
                            return `<a href="{{route('hr.employee.view')}}/${row.emp_code}" class="mt-2 btn" style="border: 1px solid var(--wb-wood); background-color: var(--wb-wood--light); font-weight: 600">${data}</a>`;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'employee_designation',
                        name: 'employee_designation'
                    },
                    {
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'is_active',
                        name: 'is_active',
                    },
                    {
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<td class="d-flex justify-content-around">
                                            <a href="{{ route('hr.employee.view') }}/${row.emp_code}" class="mx-2 text-dark" title="View"><i class="fa fa-eye" style="font-size: 15px;"></i></a>
                                            <a href="{{ route('hr.employee.manage') }}/${row.emp_code}" class="mx-2 text-success" title="Edit"><i class="fa fa-edit" style="font-size: 15px;"></i></a>
                                        <div class="mx-2 dropdown d-inline-block">
                                            <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-caret-down text-dark"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" target="_blank" onclick="return confirm('Login confirmation..')" href="{{ route('hr.employee.bypass_login') }}/${row.emp_code}">Login</a></li>
                                            </ul>
                                        </div>
                                    </td>`
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ],
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthChange: true
            });

            $('.filter-btn').on('click', function() {
                let roleId = $(this).data('role-id');
                let table = $('#serverTable').DataTable();
                table.ajax.url("{{ route('hr.employee.ajax_list') }}?role_id=" + roleId).load();
            });
        });
    </script>
@endsection
