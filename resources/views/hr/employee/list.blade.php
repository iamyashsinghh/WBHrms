@extends('hr.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading)
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
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editEmpForm" method="post" action="{{route('hr.employee.update')}}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Employee</h5>
                            <button type="button" class="close" data-bs-dismiss="modal"
                                aria-label="Close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div id="modalContent">
                                @csrf
                                <input type="hidden" name="emp_code" id="emp_code_form">
                                <div class="form-group">
                                    <label for="emp_status">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="Provision">Provision</option>
                                        <option value="Intern">Intern</option>
                                        <option value="FullTime">FullTime</option>
                                        <option value="Resign">Resign</option>
                                        <option value="Terminated">Terminated</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="punch-in-time">Punch In Time</label>
                                    <input type="time" id="punch-in-time" name="punch_in_time" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="punch-out-time">Punch Out Time</label>
                                    <input type="time" id="punch-out-time" name="punch_out_time" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="cl_left">Cl Left</label>
                                    <input type="number" id="cl_left" name="cl_left" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="pl_left">Pl Left</label>
                                    <input type="number" id="pl_left" name="pl_left" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="latings_left">Latings Left</label>
                                    <input type="number" id="latings_left" name="latings_left" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="weekdays">Weekends</label>
                                    <select name="weekdays" id="weekdays" class="form-control">
                                        <option value="null">Manual</option>
                                        <option value="sun">Sunday</option>
                                        <option value="sat-sun">Sat And Sun</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="notification_token">Notification Token</label>
                                    <input type="text" id="notification_token" name="notification_token"
                                        class="form-control" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        function openEditModal(emp_code, punch_in_time, punch_out_time, cl_left, pl_left, latings_left, notification_token,
            weekdays) {
                const formattedPunchInTime = punch_in_time ? punch_in_time.slice(0, 5) : '';
    const formattedPunchOutTime = punch_out_time ? punch_out_time.slice(0, 5) : '';

            $('#emp_code_form').val(emp_code)
            $('#punch-in-time').val(formattedPunchInTime);
    $('#punch-out-time').val(formattedPunchOutTime);
            $('#cl_left').val(cl_left)
            $('#pl_left').val(pl_left)
            $('#latings_left').val(latings_left)
            $('#notification_token').val(notification_token)
            $('#weekdays').val(weekdays)
        }
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
                        data: 'profile_img',
                        name: 'profile_img',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<a onclick="handleViewImage('${data}', '{{ route('updateProfileImage') }}/${row.emp_code}')" href="javascript:void(0);"><img class="img-thumbnail" src="${data}" style="width: 50px;" onerror="this.onerror=null; this.src='{{ asset('images/default-user.png') }}'"></a>`;
                        }
                    },
                    {
                        data: 'emp_code',
                        name: 'emp_code',
                        render: function(data, type, row) {
                            return `<a href="{{ route('hr.employee.view') }}/${row.emp_code}" class="mt-2 btn" style="border: 1px solid var(--wb-wood); background-color: var(--wb-wood--light); font-weight: 600">${data}</a>`;
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
                        searchable: false,
                        render: function(data, type, row) {
                            return `<a href="{{ route('hr.employee.is_active_status') }}/${row.emp_code}/${data == 1 ? 0 : 1}" style="font-size: 22px;"><i class="fa ${data == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'} "></i></a>`;
                        }
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
                                            <a href="{{ route('hr.employee.destroy') }}/${row.emp_code}" onclick="return confirm('Are you sure want to delete?')" class="mx-2 text-danger" title="Delete"><i class="fa fa-trash-alt" style="font-size: 15px;"></i></a>
                                        <div class="mx-2 dropdown d-inline-block">
                                            <a href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-caret-down text-dark"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" target="_blank" href="#" data-bs-toggle="modal" data-bs-target="#editModal" onclick="openEditModal('${row.emp_code}','${row.punch_in_time}','${row.punch_out_time}','${row.cl_left}','${row.pl_left}','${row.latings_left}','${row.notification_token}','${row.weekdays}', '${row.status}')">Edit</a></li>
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
