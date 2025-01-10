<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #d9534f;
            color: white;
        }
    </style>
</head>
<body>
    <h3>Attendance Sheet - {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</h3>
    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Designation</th>
                @foreach(array_keys($attendanceData[0]['attendance']) as $date)
                    <th>{{ \Carbon\Carbon::parse($date)->format('M d') }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceData as $data)
                <tr>
                    <td>{{ $data['employee_name'] }}</td>
                    <td>{{ $data['designation'] }}</td>
                    @foreach($data['attendance'] as $details)
                        <td>{{ $details['status'] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
