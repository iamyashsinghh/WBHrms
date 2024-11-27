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
    I hope this message finds you well. I am pleased to share your salary increment letter, which acknowledges your continued dedication and hard work. Please find the attached PDF containing the details of your salary adjustment.<br/><br/>

If you have any questions or need further clarification, feel free to reach out.<br/><br/>

Thank you for your contributions to our team.<br/><br/>

    Thanks & Regards<br/>
    {{env('HR_NAME')}}<br/>
    HR Executive<br/>
    Wedding Banquets Pvt Ltd<br/>
    <a href="https://weddingbanquets.in">weddingbanquets.in</a>
</body>

</html>
