<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            font-size: 20px;
            font-weight: bold;
            border-bottom: 2px solid #ccc;
            background-color: #f8f8f8;
        }

        .footer {
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
            border-top: 2px solid #ccc;
            background-color: #f8f8f8;
            margin-top: 20px;
        }

        .content {
            margin: 20px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    Dear <b>{{ $data->name }}</b><br/><br>
    I am delighted to inform you that we would like to offer you the position of <b>({{ $data->employee_designation }})</b> at Wedding
    Banquets . Your skills and qualifications impressed us, and we believe you will be a great addition to our team.<br/><br/>
    Please confirm your acceptance of this offer by replying to this email. If you have any questions or need further
    information, do not hesitate to reach out.<br/><br/>
    We look forward to welcoming you to Wedding Banquets.<br/><br/><br/>

    Thanks & Regards<br/>
    {{$hr_name->name}}<br/>
    HR Executive<br/>
    Wedding Banquets<br/>
    <a href="https://weddingbanquets.in">weddingbanquets.in</a>
</body>

</html>
