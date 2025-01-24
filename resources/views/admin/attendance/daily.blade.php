@extends('admin.layouts.app')

@section('title', 'Daily Attendance | Admin')

@section('header-css')
<link rel="stylesheet" href="{{ asset('plugins/charts/chart.css') }}">
<style>
    .attendance-container {
        max-width: 100%;
        margin: auto;
        overflow-x: auto;
    }

    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        border-radius: 10px;
        overflow: hidden;
    }

    .attendance-table th,
    .attendance-table td {
        padding: 10px;
        border: 1px solid #ddd;
        font-size: 14px;
        font-weight: normal;
    }

    .badge {
        padding: 5px;
        border-radius: 5px;
        font-size: 12px;
    }

    .badge-present {
        background-color: #28a745;
        color: white;
    }

    .badge-wo {
        background-color: #6c757d;
        color: white;
    }

    .badge-holiday {
        background-color: #17a2b8;
        color: white;
    }

    .badge-halfday {
        background-color: #ffc107;
        color: black;
    }

    .badge-absent {
        background-color: #dc3545;
        color: white;
    }
</style>
@endsection

@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1>Daily Attendance</h1>
                </div>
                <div class="col-sm-6">
                    <form class="float-right form-inline" id="dailyAttendanceForm">
                        <div class="form-group">
                            <label for="date" class="mr-2">Select Date:</label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ now()->toDateString() }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="attendance-container">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Status</th>
                            <th>Working Hours</th>
                            <th>Punch In Time</th>
                            <th>Punch Out Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dailyAttendanceBody">
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="editAttendanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAttendanceModalLabel">Edit Attendance</h5>
                <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i
                        class="fa fa-times"></i></button>
            </div>
            <div class="modal-body">
                <form id="edit-attendance-form">
                    <input type="hidden" id="employee-code" name="employee_code">
                    <input type="hidden" id="attendance-date" name="attendance_date">
                    <div class="form-group">
                        <label for="attendance-status">Status</label>
                        <select id="attendance-status" name="status" class="form-control">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="halfday">Half Day</option>
                            <option value="wo">Weekend</option>
                            <option value="holiday">Holiday</option>
                            <option value="shortleave">Short Leave</option>
                            <option value="cl">CL</option>
                            <option value="pl">PL</option>
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
                        <label for="working-hours">Working Hours</label>
                        <input type="text" id="working-hours" name="working_hours" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="desc">Note</label>
                        <input type="text" id="desc" name="desc" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="punch_in_address">Punch In Address</label>
                        <textarea type="text" id="punch_in_address" name="punch_in_address" class="form-control"
                            readonly></textarea>
                    </div>
                    <div class="form-group">
                        <label for="punch_out_address">Punch Out Address</label>
                        <textarea type="text" id="punch_out_address" name="punch_out_address" class="form-control"
                            readonly></textarea>
                    </div>
                    <div class="form-group">
                        <label for="punch_in_img">Punch In Image</label>
                        <img id="punch_in_img_preview" src="" alt="Punch In Image" class="img-thumbnail"
                            style="max-width: 100px; cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#imageModal">
                    </div>
                    <div class="form-group">
                        <label for="punch_out_img">Punch Out Image</label>
                        <img id="punch_out_img_preview" src="" alt="Punch Out Image" class="img-thumbnail"
                            style="max-width: 100px; cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#imageModal">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveAttendance" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="text-center modal-body">
                <img id="enlargedImage" src="" alt="Image Preview" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-script')
<script>
    $('#punch_in_img_preview, #punch_out_img_preview').on('click', function() {
            const imgSrc = $(this).attr('src');
            $('#enlargedImage').attr('src', imgSrc);
        });

        $(document).ready(function() {
            $('#date').change(function() {
                const date = $(this).val();
                if (!date) {
                    alert('Please select a date.');
                    return;
                }

                fetchDailyAttendance(date);
            });

            function fetchDailyAttendance(date) {
                $.ajax({
                    url: '{{ route('admin.attendance.fetch.daily') }}',
                    type: 'GET',
                    data: {
                        date
                    },
                    success: function(response) {
                        const tbody = $('#dailyAttendanceBody');
                        tbody.empty();

                        if (response.attendanceData.length === 0) {
                            tbody.append(
                                '<tr><td colspan="6">No attendance records found for the selected date.</td></tr>'
                            );
                            return;
                        }

                        response.attendanceData.forEach(function(data) {
                            const employee = data.employee;
                            const status = data.status || '--';
                            const badgeClass = getBadgeClass(status);
                            const row = `
                            <tr>
                                <td>${employee.name}</td>
                                <td><span class="badge ${badgeClass}">${status}</span></td>
                                <td>${data.working_hours}</td>
                                <td>${data.punch_in_time}</td>
                                <td>${data.punch_out_time}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-btn" data-emp-code="${employee.emp_code}" data-date="${date}" data-status="${status}" data-punch-in="${data.punch_in_time}" data-punch-out="${data.punch_out_time}" data-working-hours="${data.working_hours}">Edit</button>
                                </td>
                            </tr>`;
                            tbody.append(row);
                        });

                        $('.edit-btn').click(function() {
                            const empCode = $(this).data('emp-code');
                            const date = $(this).data('date');

                            $.ajax({
                                url: `{{ route('admin.attendance.get') }}/${empCode}/${date}`,
                                type: 'GET',
                                success: function(response) {

                                    if (response.success) {
                                        $('#employee-code').val(empCode);
                                    $('#attendance-date').val(date);
                                        const attendance = response.attendance;
                                        $('#attendance-status').val(attendance
                                            .status);
                                        $('#punch-in-time').val(attendance
                                            .punch_in_time);
                                        $('#punch-out-time').val(attendance
                                            .punch_out_time);
                                        $('#working-hours').val(
                                            attendance.working_hours ||
                                            calculateWorkingHours(attendance
                                                .punch_in_time, attendance
                                                .punch_out_time)
                                        );
                                        $('#desc').val(attendance.desc);
                                        $('#punch_in_address').val(attendance
                                            .punch_in_address);
                                        $('#punch_out_address').val(attendance
                                            .punch_out_address);
                                        if (attendance.punch_in_img) {
                                            $('#punch_in_img_preview')
                                                .attr('src', `{{ asset('storage') }}/${attendance.punch_in_img}`)
                                                .show();
                                        } else {
                                            $('#punch_in_img_preview')
                                        .hide();
                                        }

                                        if (attendance.punch_out_img) {
                                            $('#punch_out_img_preview')
                                                .attr('src', `{{ asset('storage') }}/${attendance.punch_out_img}`)
                                                .show();
                                        } else {
                                            $('#punch_out_img_preview')
                                        .hide();
                                        }
                                    } else {
                                        $('#employee-code').val(empCode);
                                    $('#attendance-date').val(date);
                                        $('#attendance-status').val('');
                                        $('#punch-in-time').val('');
                                        $('#punch-out-time').val('');
                                        $('#working-hours').val('');
                                        $('#desc').val('');
                                        $('#punch_in_address').val('');
                                        $('#punch_out_address').val('');
                                        $('#punch_in_img_preview').hide();
                                        $('#punch_out_img_preview').hide();

                                    }
                                    $('#editAttendanceModal').modal('show');
                                },
                                error: function() {
                                    alert('Error fetching attendance data.');
                                }
                            });
                        });

                        function calculateWorkingHours(punchIn, punchOut) {
                            if (punchIn && punchOut) {
                                const start = moment(punchIn, 'HH:mm');
                                const end = moment(punchOut, 'HH:mm');
                                const duration = moment.duration(end.diff(start));
                                const hours = Math.floor(duration.asHours());
                                const minutes = duration.minutes();
                                return `${hours}:${minutes < 10 ? '0' : ''}${minutes}`;
                            }
                            return '--';
                        }

                    },
                    error: function() {
                        alert('Error fetching attendance data.');
                    }
                });
            }

            $('#saveAttendance').click(function() {
                const formData = $('#edit-attendance-form').serialize();

                const employeeCode = $('#employee-code').val();
    const attendanceDate = $('#attendance-date').val();

                $.ajax({
                    url: `{{ route('admin.attendance.store') }}/${employeeCode}/${attendanceDate}`,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#editAttendanceModal').modal('hide');
                            fetchDailyAttendance($('#date').val());
                            alert('Attendance updated successfully.');
                        } else {
                            alert('Failed to update attendance.');
                        }
                    },
                    error: function() {
                        alert('Error updating attendance.');
                    }
                });
            });

            function getBadgeClass(status) {
                switch (status) {
                    case 'present':
                        return 'badge-present';
                    case 'absent':
                        return 'badge-absent';
                    case 'halfday':
                        return 'badge-halfday';
                    case 'wo':
                        return 'badge-wo';
                    case 'holiday':
                        return 'badge-holiday';
                        case 'cl':
                        return 'badge-holiday';
                        case 'pl':
                        return 'badge-holiday';
                    default:
                        return '';
                }
            }

            fetchDailyAttendance($('#date').val());
        });
</script>
@endsection
