<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Salary Slip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 10px;
        }

        .header {
            text-align: center;
        }

        .header h1,
        .header p {
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        .footer .signature {
            margin-top: 40px;
        }

        .border {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .stamp_img {
            height: 200px;
            position: absolute;
            bottom: 35%;
            right: -5%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>WISE WEDDINGS PVT LTD</h1>
            <p>Shivaji Marg, Meenakshi Garden, Subhash Nagar, New Delhi, Delhi 110018</p>
            <p><strong>Payslip for: </strong> 15{{ \Carbon\Carbon::parse($data['startDate'])->format('M') }} - 14{{
                \Carbon\Carbon::parse($data['endDate'])->format('M Y') }}</p>
            <p><strong>Location:</strong> HEAD OFFICE</p>
        </div>

        <table class="table">
            <tr>
                <th>Employee Code</th>
                <td>{{ $employee['emp_code'] }}</td>
                <th>Salary Mode</th>
                <td>Cash</td>
            </tr>
            <tr>
                <th>Name of Employee</th>
                <td>{{ $employee['name'] }}</td>
                <th>Bank A/c No</th>
                <td>{{$employee['account_number']}}</td>
            </tr>
            <tr>
                <th>D.O.J</th>
                <td>{{ \Carbon\Carbon::parse($employee['doj'])->format('d M, Y') }}</td>
                <th>PF No</th>
                <td>{{$employee['pf_number'] ?? 'N/A'}}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $employee['employee_designation'] }}</td>
                <th>ESI No</th>
                <td>{{$employee['esi_number'] ?? 'N/A'}}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $employee['department'] }}</td>
                <th>PAN No</th>
                <td>{{ $employee['pan_number']?? 'N/A'}}</td>
            </tr>
            <tr>
                <th></th>
                <td></td>
                <th>UAN No</th>
                <td>{{$employee['uan'] ?? 'N/A'}}</td>
            </tr>
        </table>
        <table class="salary-table" style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;">
            <thead>
                <tr>
                    <th style="width: 20%; text-align: left; border: 1px solid black;" rowspan="2">Days</th>
                    <th style="width: 40%; text-align: center; border: 1px solid black;" colspan="3">EARNINGS</th>
                    <th style="width: 20%; text-align: center; border: 1px solid black;" colspan="2">DEDUCTIONS</th>
                </tr>
                <tr>
                    <th style="text-align: left; border: 1px solid black;">Particulars</th>
                    <th style="text-align: right; border: 1px solid black;">Rates</th>
                    <th style="text-align: right; border: 1px solid black;">Payable</th>
                    <th style="text-align: left; border: 1px solid black;">Particulars</th>
                    <th style="text-align: right; border: 1px solid black;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border">Total Days: {{ $data['total_days'] }}</td>
                    <td class="border">Basic Pay</td>
                    <td style="text-align: right;" class="border">{{ number_format($salaries['Basic'], 2) }}</td>
                    <td style="text-align: right;">{{ number_format($paying_salaries['Basic'], 2) }}</td>
                    <td class="border">E.P.F.</td>
                    <td style="text-align: right;" class="border">0.00</td>
                </tr>
                <tr>
                    <td class="border">Present: {{ $data['present'] }}</td>
                    <td class="border">H.R.A</td>
                    <td style="text-align: right;" class="border">{{ number_format($salaries['HRA'], 2) }}</td>
                    <td style="text-align: right;" class="border">{{ number_format($paying_salaries['HRA'], 2) }}</td>
                    <td class="border">L.W.F.</td>
                    <td style="text-align: right;" class="border">0.00</td>
                </tr>
                <tr>
                    <td class="border">Absent: {{ $data['absent'] }}</td>
                    <td class="border">Allowance</td>
                    <td style="text-align: right;" class="border">{{ number_format($salaries['Allowance'], 2) }}</td>
                    <td style="text-align: right;" class="border">{{ number_format($paying_salaries['Allowance'], 2) }}
                    </td>
                    <td class="border">Income Tax</td>
                    <td style="text-align: right;" class="border">0.00</td>
                </tr>
                <tr>
                    <td class="border">Holidays: {{ $data['holiday'] }}</td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                </tr>
                <tr>
                    <td class="border">WeekOff: {{ $data['wo'] }}</td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                </tr>
                <tr>
                    <td class="border">C.L: {{ $data['cl']}}</td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                </tr>
                <tr>
                    <td class="border">P.L.: {{$data['pl']}}</td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                </tr>
                <tr>
                    <td class="border">Halfday: {{$data['halfday']}}</td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                    <td class="border"></td>
                    <td style="text-align: right;" class="border"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;">Payable: {{ $data['total_present'] }}</td>
                    <td style="border: 1px solid black;"><strong>Total</strong></td>
                    <td style="text-align: right; border: 1px solid black;">{{ number_format($total_salary, 2) }}</td>
                    <td style="text-align: right; border: 1px solid black;">{{ number_format($salary_to_be_paid, 2) }}
                    </td>
                    <td style="border: 1px solid black"></td>
                    <td style="text-align: right; border: 1px solid black;">0.00</td>
                </tr>
            </tbody>
        </table>
        <div class="footer">
            @php
            $rounded_salary = round($salary_to_be_paid, 2);
            $salary_in_words = \NumberFormatter::create('en', \NumberFormatter::SPELLOUT)->format($rounded_salary);
            $salary_in_words = ucfirst($salary_in_words);
            @endphp
            <p><strong>Net Payable:</strong> Rs. {{ number_format($salary_to_be_paid, 2) }}</p>
            <p><strong>In words:</strong> {{$salary_in_words}} rupees only</p>
            <img src="https://wbcrm.in/wb_stamp_signjhgvcxgfhtyjgnbvchfgn.png" class="stamp_img">
        </div>
    </div>
</body>

</html>
