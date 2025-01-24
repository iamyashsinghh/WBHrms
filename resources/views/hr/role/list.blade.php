@extends('hr.layouts.app')
@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | Venue CRM')
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
                <button class="btn text-light btn-sm buttons-print" onclick="handle_role_add()"
                    style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</button>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="clientTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th class="text-nowrap">ID</th>
                            <th class="text-nowrap">Role Name</th>
                            <th class="text-nowrap">Punch In Time</th>
                            <th class="text-nowrap">Puch Out Time</th>
                            <th class="text-nowrap">Grace Time</th>
                            <th class="text-nowrap">Lating Time</th>
                            <th class="text-nowrap">Login Start Time</th>
                            <th class="text-nowrap">Login End Time</th>
                            <th class="text-nowrap">All Time Login</th>
                            <th class="text-nowrap">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td class="text-bold">{{ $role->name }}</td>
                            <td>
                                <input type="time" class="form-control puch-time" data-role-id="{{ $role->id }}"
                                    data-type="punchin" value="{{ $role->punch_in_time }}">
                            </td>
                            <td>
                                <input type="time" class="form-control puch-time" data-role-id="{{ $role->id }}"
                                    data-type="punchout" value="{{ $role->punch_out_time }}">
                            </td>
                            <td>
                                <input type="time" class="form-control grace_time" data-role-id="{{ $role->id }}"
                                    value="{{ $role->grace_time }}">
                            </td>
                            <td>
                                <input type="time" class="form-control lating_time" data-role-id="{{ $role->id }}"
                                     value="{{ $role->lating_time }}">
                            </td>
                            <td>
                                <input type="time" class="form-control login-time" data-role-id="{{ $role->id }}"
                                    data-type="start" value="{{ $role->login_start_time }}">
                            </td>
                            <td>
                                <input type="time" class="form-control login-time" data-role-id="{{ $role->id }}"
                                    data-type="end" value="{{ $role->login_end_time }}">
                            </td>
                            <td>
                                <a href="{{route('hr.role.update.isAllTimeLogin')}}/{{$role->id}}/{{$role->is_all_time_login == 1 ? 0 : 1}}"
                                    style="font-size: 22px;"><i
                                        class="fa {{$role->is_all_time_login == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger'}} "></i></a>
                            </td>
                            <td>{{ date('d-M-Y', strtotime($role->created_at)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('.login-time').on('change', function () {
            const roleId = $(this).data('role-id');
            const type = $(this).data('type');
            const value = $(this).val();

            $.ajax({
                url: `{{ route('hr.role.updateLoginTime') }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    role_id: roleId,
                    type: type,
                    value: value
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success('Login time updated successfully.');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error('An error occurred while updating the login time.');
                }
            });
        });
        $('.puch-time').on('change', function () {
            const roleId = $(this).data('role-id');
            const type = $(this).data('type');
            const value = $(this).val();

            $.ajax({
                url: `{{ route('hr.role.updatePunchTime') }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    role_id: roleId,
                    type: type,
                    value: value
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success('Puch time updated successfully.');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error('An error occurred while updating the puch time.');
                }
            });
        });
        $('.grace_time').on('change', function () {
            const roleId = $(this).data('role-id');
            const value = $(this).val();

            $.ajax({
                url: `{{ route('hr.role.updateGraceTime') }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    role_id: roleId,
                    value: value
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success('Grace time updated successfully.');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error('An error occurred while updating the grace time.');
                }
            });
        });
        $('.lating_time').on('change', function () {
            const roleId = $(this).data('role-id');
            const value = $(this).val();

            $.ajax({
                url: `{{ route('hr.role.updateLatingTime') }}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    role_id: roleId,
                    value: value
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success('Lating time updated successfully.');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (response) {
                    toastr.error('An error occurred while updating the lating time.');
                }
            });
        });
    });
</script>
@endsection
