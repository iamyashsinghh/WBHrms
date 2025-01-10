<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #891010;
            color: white;
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
</head>
<body>
    <h2>Attendance Report for {{ $employee->name }}</h2>
    <p>Generated at: {{ now()->format('Y-m-d H:i:s') }}</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Working Hours</th>
                <th>Punch In Time</th>
                <th>Punch Out Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailedAttendance as $date => $details)
                <tr>
                    <td>{{ $date }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($details['status']) }}">
                            {{ ucfirst($details['status']) }}
                        </span>
                    </td>
                    <td>{{ $details['working_hours'] }}</td>
                    <td>{{ $details['punch_in_time'] }}</td>
                    <td>{{ $details['punch_out_time'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
