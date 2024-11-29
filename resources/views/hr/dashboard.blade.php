@extends('hr.layouts.app')
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
</style>
@endsection
@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">Monthly Staff</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="mb-4">
                <form action="javascript:void(0);" id="filter-form" class="form-inline">
                    <div class="mb-2 form-group">
                        <label for="month" class="mr-2">Month:</label>
                        <select name="month" id="month" class="form-control">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2 form-group mx-sm-3">
                        <label for="year" class="mr-2">Year:</label>
                        <select name="year" id="year" class="form-control">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
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
@endsection
@section('footer-script')
<script>
     $(document).ready(function () {
        $('#filter-button').click(function () {
            const month = $('#month').val();
            const year = $('#year').val();
            fetchAttendance(month, year);
        });

        function fetchAttendance(month, year) {
            $.ajax({
                url: `{{ route('hr.attendance.fetch') }}`,
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
                                        <div>${employee.name}</div>
                                    </button>
                                </td>
                                <td>${employee.present_days ?? '-'}</td>
                                <td>${employee.absent_days ?? '-'}</td>
                                <td>${employee.half_days ?? '-'}</td>
                                <td>${employee.paid_leaves ?? '-'}</td>
                                <td>${employee.unmarked_days ?? '-'}</td>
                                <td>${employee.total_attendance ?? '-'}</td>
                            </tr>
                            <tr class="accordion-content" id="details-${employee.id}">
                                <td colspan="9">
                                    <table class="attendance-table">
                                        <thead>
                                            <tr class="table-dark">
                                                <th>DAYS</th>
                                                ${daysInRange.map(date => {
                                                    const day = new Date(date).getDate();
                                                    return `<th>${day}</th>`;
                                                }).join('')}
                                            </tr>
                                            <tr class="table-dark">
                                                <th>Working Hours</th>
                                                ${daysInRange.map(date => {
                                                    const workingHours = detailedAttendance[date]?.working_hours ?? '--';
                                                    return `<th>${workingHours}</th>`;
                                                }).join('')}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Attendance</td>
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

                                                    return `<td><span class="badge ${badgeClass}">${attendanceStatus}</span></td>`;
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
        fetchAttendance($('#month').val(), $('#year').val());
    });
</script>
@endsection
