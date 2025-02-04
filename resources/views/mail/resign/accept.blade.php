<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resign Accept</title>
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
    This is to inform you that your resignation is accepted and the last working day will be marked as {{$last_working_day}}<br/><br/>
    Your attention is also drawn to the surviving obligations of your employment with company and you are urged to remain compliant with all such surviving obligations, including but not limited to Code of Conduct, Confidentiality, Non-Disclosure, Non-compete and Non-Disparagement obligations, etc., in accordance with the Letter of Appointment and employment policies and procedures that were applicable on you.<br/><br/>
    Please ensure that all company property in your possession or issued to you, including but not limited to laptops, mobile devices, passwords, data, and other assets, is returned immediately to the address provided in the exit formalities. This will allow us to process your full and final settlement within 45 working days from the date of asset submission.<br/><br/><br/>
    Thanks & Regards<br/>
    {{$hr_name->name}}<br/>
    HR Executive<br/>
    Wedding Banquets<br/>
    <a href="https://weddingbanquets.in">weddingbanquets.in</a>
</body>

</html>
