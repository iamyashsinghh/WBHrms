@extends('hr.layouts.app')
@section('title', 'Dashboard | Admin')
@section('header-css')
<link rel="stylesheet" href="{{ asset('plugins/charts/chart.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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

    .accordion-button {
        cursor: pointer;
        background: none;
        border: none;
        text-align: left;
        font-size: 14px;
    }

    .accordion-icon {
        font-size: 18px;
        transition: transform 0.3s ease;
    }

    .accordion-icon.rotate {
        transform: rotate(90deg);
    }

    .accordion-content {
        display: none;
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

    @media only screen and (max-width: 768px) {
        .attendance-container {
            width: 100%;
            margin: 0;
            overflow-x: auto;
        }

        .attendance-table th,
        .attendance-table td {
            font-size: 12px;
            padding: 8px;
        }
    }

    .highlight-light {
        background-color: var(--wb-renosand) !important;
        color: #fff !important;
    }

    .table-dark,
    .table-dark>td,
    .table-dark>th {
        background-color: var(--wb-dark-red);
        color: #fff !important;
    }
</style>
@endsection
@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <form action="javascript:void(0);" id="filter-form" class="form-inline">
                    <div class="mb-2 form-group">
                        <select name="month" id="month" class="form-control">
                            @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month==$m ? 'selected' : '' }}>
                                {{\Carbon\Carbon::createFromDate(null, $m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2 form-group mx-sm-1">
                        <select name="year" id="year" class="form-control">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}" {{ $year==$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" id="filter-button" class="mb-2 btn btn-primary">Filter</button>
                </form>
            </div>
            <div class="attendance-container">
                <table class="attendance-table ">
                    <thead>
                        <tr class="table-dark" style="color: #000;">
                            <th>Staff Name</th>
                            <th>Present</th>
                            <th>Absent</th>
                            <th>Half Day</th>
                            <th>Paid Leave</th>
                            <th>Unmarked</th>
                            <th>Total Attendance</th>
                        </tr>
                    </thead>
                    <tbody id="attendance-body">
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
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-attendance-form">
                    <input type="hidden" id="employee-code" name="employee_code">
                    <input type="hidden" id="attendance-date" name="attendance_date">
                    <div class="form-group">
                        <label for="attendance-date-display">Date</label>
                        <input type="text" id="attendance-date-display" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="attendance-status">Status</label>
                        <select id="attendance-status" name="status" class="form-control">
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="halfday">Half Day</option>
                            <option value="weekend">Weekend</option>
                            <option value="holiday">Holiday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="punch-in-time">Punch In Time</label>
                        <input type="time" id="punch-in-time" name="punch_in_time" class="form-control" step="60">
                    </div>
                    <div class="form-group">
                        <label for="punch-out-time">Punch Out Time</label>
                        <input type="time" id="punch-out-time" name="punch_out_time" class="form-control" step="60">
                    </div>
                    <div class="form-group">
                        <label for="desc">Note</label>
                        <input type="text" id="desc" name="desc" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="working-hours">Working Hours</label>
                        <input type="text" id="working-hours" name="working_hours" class="form-control" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    aria-label="Close">Close</button>
                <button type="button" id="save-attendance" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('footer-script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>

    $(document).ready(function () {
    $('#filter-button').click(function () {
        const month = $('#month').val();
        const year = $('#year').val();
        fetchAttendance(month, year);
    });

    function fetchAttendance(month, year) {
        $.ajax({
            url: '{{ route('hr.attendance.fetch') }}',
            type: 'GET',
            data: {
                month: month,
                year: year
            },
            success: function (response) {
                const tbody = $('#attendance-body');
                tbody.empty();
                response.attendanceData.forEach(function (data) {
                    const employee = data.employee;
                    const detailedAttendance = data.detailed_attendance;

                    let daysInRange = Object.keys(detailedAttendance);

                    let row = `
                        <tr>
                            <td><button class="accordion-button" data-toggle="collapse" data-target="#details-${employee.id}" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
                                    <i class="accordion-icon fas fa-chevron-right"></i>
                                    <div>${employee.name}<span class="mx-3 badge bg-primary">${employee.emp_type}</span></div>
                                </button>
                            </td>
                            <td>${data.present_days ?? '-'}</td>
                            <td>${data.absent_days ?? '-'}</td>
                            <td>${data.half_days ?? '-'}</td>
                            <td>${data.paid_leaves ?? '-'}</td>
                            <td>${data.unmarked_days ?? '-'}</td>
                            <td>${data.total_attendance ?? '-'}</td>
                        </tr>
                        <tr class="accordion-content" id="details-${employee.id}">
                            <td colspan="9">
                                <table class="attendance-table">
                                    <thead>
                                        <tr class="table-dark">
                                            <th>DAYS</th>
                                            ${daysInRange.map(date => {
                                                const day = new Date(date).getDate();
                                                const dayShort = new Date(date).toLocaleString('en-US', { weekday: 'short' });
                                                let highlightClass = '';
                                                const currentDate = new Date(date);
                                                const monthEnd = new Date(response.year, response.month - 1, 14, 23, 59, 59);

                                                if (employee.emp_type === 'Fulltime' && currentDate >= new Date(response.year, response.month - 2, 15) && currentDate <= monthEnd) {
                                                    highlightClass = 'highlight-light';
                                                } else if (employee.emp_type === 'Intern') {
                                                    highlightClass = 'highlight-light';
                                                }
                                                return `<th class="${highlightClass}">${day} ${dayShort}</th>`;
                                            }).join('')}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="table-dark">Attendance</td>
                                            ${daysInRange.map(date => {
                                                const attendanceStatus = detailedAttendance[date]?.status ?? '--';
                                                let badgeClass = '';

                                                switch (attendanceStatus) {
                                                    case 'P': badgeClass = 'badge-present'; break;
                                                    case 'WO': badgeClass = 'badge-wo'; break;
                                                    case 'HO': badgeClass = 'badge-holiday'; break;
                                                    case 'H': badgeClass = 'badge-halfday'; break;
                                                    case 'A': badgeClass = 'badge-absent'; break;
                                                    default: badgeClass = '';
                                                }

                                                return `<td><span class="badge ${badgeClass}" data-date="${date}" data-employee="${employee.emp_code}" onclick="editAttendance(this)">${attendanceStatus}</span></td>`;
                                            }).join('')}
                                        </tr>
                                        <tr >
                                            <th class="table-dark" title="Working Hours">WH</th>
                                            ${daysInRange.map(date => {
                                                const workingHours = detailedAttendance[date]?.working_hours ?? '--';
                                                return `<th>${workingHours}</th>`;
                                            }).join('')}
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>`;
                    tbody.append(row);
                });
                $('.accordion-button').click(function () {
                    $(this).find('.accordion-icon').toggleClass('rotate');
                    const target = $(this).attr('data-target');
                    $(target).toggle();
                });
            }
        });
    }

    window.editAttendance = function(element) {
        const date = $(element).data('date');
        const empCode = $(element).data('employee');

        $.ajax({
            url: `{{ route('hr.attendance.get') }}/${empCode}/${date}`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const attendance = response.attendance;

                    $('#attendance-date').val(date);
                    $('#employee-code').val(empCode);
                    $('#attendance-status').val(attendance.status);
                    $('#punch-in-time').val(attendance.punch_in_time);
                    $('#punch-out-time').val(attendance.punch_out_time);
                    $('#working-hours').val(attendance.working_hours || calculateWorkingHours(attendance.punch_in_time, attendance.punch_out_time));
                    $('#desc').val(attendance.desc);
                    $('#attendance-date-display').val(date);

                } else {
                    $('#attendance-date').val(date);
                    $('#employee-code').val(empCode);
                    $('#attendance-status').val('');
                    $('#punch-in-time').val('');
                    $('#punch-out-time').val('');
                    $('#working-hours').val('');
                    $('#desc').val('');
                    $('#attendance-date-display').val(date);
                }
                $('#editAttendanceModal').modal('show');
            }
        });
    };

    $('#save-attendance').click(function () {
    const empCode = $('#employee-code').val();
    const date = $('#attendance-date').val();
    const formData = $('#edit-attendance-form').serialize();

    $.ajax({
        url: `{{ route('hr.attendance.store') }}/${empCode}/${date}`,
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.success) {
                $('#editAttendanceModal').modal('hide');
                const month = $('#month').val();
                const year = $('#year').val();
                fetchAttendance(month, year);
                toastr.success('Attendance updated successfully');
            } else {
                toastr.error('Failed to update attendance');
            }
        },
        error: function (xhr) {
            toastr.error('An error occurred. Please try again.');
        }
    });
   });


    $('#punch-in-time, #punch-out-time').change(function () {
        const punchIn = $('#punch-in-time').val();
        const punchOut = $('#punch-out-time').val();
        const workingHours = calculateWorkingHours(punchIn, punchOut);
        $('#working-hours').val(workingHours);
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

    fetchAttendance($('#month').val(), $('#year').val());
    });

</script>
@endsection
