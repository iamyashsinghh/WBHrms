<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-size: 20px;
            line-height: 10px;
            font-weight: 300;
            line-height: 16px;
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
        }

        h4 {
            font-size: 16px;
            font-weight: bold;
            line-height: 0px !important;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 170px;
        }

        .header img {
            height: 170px;
            width: 100%;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
        }

        .footer img {
            height: 80px;
            width: 100%;
        }

        .content {
            padding: 180px 50px 120px 50px;
            margin: 0;
        }

        .content2 {
            padding: 180px 50px 120px 50px;
            margin: 0;
        }

        .page-break {
            page-break-after: always;
        }

        .custom-table {
            width: 100%;
            border: 1px solid #dee2e6;
            border-collapse: collapse;
        }

        .custom-table th,
        .custom-table td {
            border: 2px solid #000;
            padding: 8px;
        }

        .custom-table thead th {
            background-color: #a50000;
            color: #fff;
        }

        .custom-table tfoot th {
            background-color: #a50000;
            color: #fff;
        }

        .table-responsive-custom {
            overflow-x: auto;
        }

        .signature-section {
            width: 100%;
            padding: 0 50px 0 50px;
            font-family: Arial, sans-serif;
            position: relative;
        }

        .left-section {
            float: left;
            width: 45%;
        }

        .right-section {
            float: right;
            width: 35%;
            text-align: left;
        }

        .stamp {
            width: 130px;
            height: auto;
        }

        .date-place {
            margin-top: 20px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    @php
            use Carbon\Carbon;
    $formattedDate = Carbon::now()->format('F Y');
    @endphp
    <!-- Header -->
    <div class="header">
        <img src="https://wbcrm.in/offerletterheader.png" alt="Header Image" loading="eager">
    </div>
    <div class="footer">
        <img src="https://wbcrm.in/offerletterfooter.png" alt="Footer Image" loading="eager">
    </div>

    <div class="content">
        <h2 style="color:#891010; text-decoration: underline; font-family: auto; text-align: center; margin-top: 50px;">
            <b>INCREMENT LETTER</b>
        </h2>

        <div style="margin-top: 40px;">
            Dear <b>{{$data->name}}</b>,<br /><br />
            Congratulations on completing a milestone with Wedding Banquets! We are delighted to recognize your hard work and dedication, which have been integral to the success of our company.<br /><br />
            We are pleased to inform you that, effective <b>{{$formattedDate}}</b>, your salary will be increased by <b>Rs. {{$data->inc_amt}}</b>. Your new monthly salary will be <b>Rs. {{$data->new_salary}}</b>. This increment reflects our appreciation for your outstanding performance and commitment throughout the past year.<br /><br />

            Your contributions have been exemplary, and your valuable advice and guidance have significantly impacted our growth. We deeply appreciate your efforts and the professionalism you bring to your role.

            This letter serves as formal notification of your salary increment and as a gesture of gratitude from the management team.

            Once again, thank you for your dedication and excellent work. We look forward to seeing your continued success and contributions in the years to come.<br />
            Warm regards,<br /><br />

            Sincerely,<br />
            {{$hr_name->name}}<br />
            Hr Executive<br />
            <a href="https://weddingbanquets.in"><b>Wedding Banquets</b></a>
        </div>
        <div class="clearfix signature-section" style="margin-top: 20px">
            <div class="right-section" style="margin-top: 50px">
                <img src="https://wbcrm.in/wb_stamp_signjhgvcxgfhtyjgnbvchfgn.png" alt="Stamp" class="stamp">
            </div>
        </div>
    </div>
</body>

</html>
