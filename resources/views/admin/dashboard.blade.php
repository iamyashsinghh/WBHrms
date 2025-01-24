@extends('admin.layouts.app')
@section('title', 'Dashboard | Admin')
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
            <div class="row" id="admin-dashboard-cards">
                <div class="col-lg-3 col-6">
                    <div class="d-block">
                        <div class="text-md small-box text-light" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$total_users}}</h3>
                                <p>Total Employee</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.employee.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                        <div class="text-md small-box text-light" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$total_users}}</h3>
                                <p>Total Employee</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.employee.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9">
                    <div class="text-xs card">
                        <div class="border-0 card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title row">
                                <i class="mr-1 fas fa-th"></i>
                                Daily Attendance
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas class="chart" id="preDayAttendance"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <div class="mb-2 d-flex justify-content-end">
                    <button id="toggle-all" class="btn btn-primary"
                        style="background-color: #891010; border: none;">Open All</button>
                </div>
                <div class=" dropdown">
                    <button class="mx-2 btn btn-primary dropdown-toggle" type="button" id="downloadAttendanceDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        style="background-color: #891010; border: none;">
                        Download Attendance Sheet
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="downloadAttendanceDropdown">
                        <li>
                            <a class="dropdown-item download-format" href="#" data-format="excel">Download as Excel</a>
                        </li>
                        <li>
                            <a class="dropdown-item download-format" href="#" data-format="pdf">Download as PDF</a>
                        </li>
                    </ul>
                </div>
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
                    <button type="button" id="filter-button" class="mb-2 btn btn-primary"
                        style="background-color: #891010; border: none;">Filter</button>
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
    <div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog"
        aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
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
                                <option value="wo">Weekend</option>
                                <option value="holiday">Holiday</option>
                                <option value="shortleave">Short Leave</option>
                                <option value="cl">CL</option>
                                <option value="pl">PL</option>
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
</div>
@section('footer-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const get_last_day_of_the_month = Number("{{ date('t') }}");

const current_month_days_arr = [];
for (let i = 1; i <= get_last_day_of_the_month; i++) {
    current_month_days_arr.push(`${i}-{{ date('M') }}`)
}


new Chart("preDayAttendance", {
            type: "line",
            data: {
                labels: current_month_days_arr,
                datasets: [
                    {
                        label: 'Total Attendance',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#891010",
                        borderColor: "#891010",
                        data: "{{ $preDayAttendance }}".split(","),
                    },
                    {
                        label: 'Present',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#01F702",
                        borderColor: "#01F702",
                        data: "{{ $preDayAttendancePresent }}".split(","),
                    },
                    {
                        label: 'Absent',
                        fill: false,
                        tension: 0,
                        backgroundColor: "red",
                        borderColor: "red",
                        data: "{{ $preDayAttendanceAbsent }}".split(","),
                    },
                    {
                        label: 'Halfday',
                        fill: false,
                        tension: 0,
                        backgroundColor: "#F7D100",
                        borderColor: "#F7D100",
                        data: "{{ $preDayAttendanceHalfday }}".split(","),
                    },
                    {
                        label: 'Weak Off',
                        fill: false,
                        tension: 0,
                        backgroundColor: "grey",
                        borderColor: "grey",
                        data: "{{ $preDayAttendanceWo }}".split(","),
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                        position: "top",
                    },
                    tooltip: {
                        mode: "index",
                        intersect: false,
                        callbacks: {
                            label: function(tooltipItem) {
                                const label = tooltipItem.dataset.label || "";
                                const value = tooltipItem.raw;
                                return `${label}: ${value}`;
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: "category",
                        title: {
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                        },
                        ticks: {
                            min: 1,
                        },
                    },
                },
            },
        });


    $(document).ready(function () {
        $('#filter-button').click(function () {
            const month = $('#month').val();
            const year = $('#year').val();
            fetchAttendance(month, year);
        });

        function fetchAttendance(month, year) {
            $.ajax({
                url: '{{ route('admin.attendance.fetch') }}',
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
                                            <div class="mb-2 d-flex">
            <button
                class="btn btn-primary btn-sm download-attendance"
                data-employee-id="${employee.id}"
                data-employee-name="${employee.name}"
                data-format="pdf"
                style="background-color: #891010; border: none;">
                Download PDF
            </button>
            <button
                class="mx-2 btn btn-primary btn-sm download-attendance"
                data-employee-id="${employee.id}"
                data-employee-name="${employee.name}"
                data-format="excel"
                style="background-color: #891010; border: none;">
                Download Excel
            </button>
        </div>

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
                                                        default: badgeClass = 'badge-holiday';
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

                    $('#toggle-all').off('click').on('click', function () {
                    const isOpen = $(this).text() === 'Open All';
                    $('.accordion-content').toggle(isOpen);
                    $('.accordion-icon').toggleClass('rotate', isOpen);
                    $(this).text(isOpen ? 'Close All' : 'Open All');
                });


                }
            });
        }

        window.editAttendance = function(element) {
            const date = $(element).data('date');
            const empCode = $(element).data('employee');

            $.ajax({
                url: `{{ route('admin.attendance.get') }}/${empCode}/${date}`,
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
                        $('#punch_in_address').val(attendance.punch_in_address);
                        $('#punch_out_address').val(attendance.punch_out_address);
                        $('#attendance-date-display').val(date);

                    } else {
                        $('#attendance-date').val(date);
                        $('#employee-code').val(empCode);
                        $('#attendance-status').val('');
                        $('#punch-in-time').val('');
                        $('#punch-out-time').val('');
                        $('#working-hours').val('');
                        $('#desc').val('');
                        $('#punch_in_address').val('');
                        $('#punch_out_address').val('');
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
            url: `{{ route('admin.attendance.store') }}/${empCode}/${date}`,
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

        $(document).on('click', '.download-attendance', function () {
    const employeeId = $(this).data('employee-id');
    const employeeName = $(this).data('employee-name');
    const format = $(this).data('format');

    // Get selected month and year from dropdowns
    const month = $('#month').val();
    const year = $('#year').val();

    // Show loading spinner
    const button = $(this);
    button.html('<i class="fas fa-spinner fa-spin"></i> Downloading...');
    button.prop('disabled', true);

    // AJAX request
    $.ajax({
        url: `{{ route('admin.attendance.download') }}`,
        type: 'POST',
        data: {
            employee_id: employeeId,
            format: format,
            month: month,
            year: year,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            // Correct file extension
            const fileExtension = format === 'excel' ? 'xlsx' : format;

            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = response.file_url;
            link.download = `${employeeName}-attendance.${fileExtension}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            button.html(`Download ${format.toUpperCase()}`);
            button.prop('disabled', false);
        },
        error: function () {
            alert('Error generating file. Please try again.');
            button.html(`Download ${format.toUpperCase()}`);
            button.prop('disabled', false);
        }
    });
});
$(document).on('click', '.download-format', function (e) {
    e.preventDefault();

    const format = $(this).data('format');
    const month = $('#month').val();
    const year = $('#year').val();

    if (!month || !year) {
        alert('Please select both month and year.');
        return;
    }

    const button = $('#downloadAttendanceDropdown');
    button.html('<i class="fas fa-spinner fa-spin"></i> Generating...');
    button.prop('disabled', true);

    $.ajax({
        url: '{{ route("admin.attendance.generate") }}',
        type: 'POST',
        data: {
            month: month,
            year: year,
            format: format,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            const fileExtension = format === 'excel' ? 'xlsx' : format;

            button.html('Download Attendance Sheet');
            button.prop('disabled', false);

            // Create a temporary link to download the file
            const link = document.createElement('a');
            link.href = response.file_url;
            link.download = `attendance-sheet.${fileExtension}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        error: function () {
            alert('Failed to generate the attendance sheet. Please try again.');
            button.html('Download Attendance Sheet');
            button.prop('disabled', false);
        }
    });
});


</script>
@endsection
@endsection
