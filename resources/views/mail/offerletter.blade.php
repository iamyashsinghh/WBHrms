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
    <div class="header">
        Offer Letter from {{ $data['company_name'] }}
    </div>

    <div class="content">
        <h1>Welcome {{ $data['candidate_name'] }}!</h1>
        <p>We are delighted to offer you the position of <strong>{{ $data['position'] }}</strong> at <strong>{{ $data['company_name'] }}</strong>.</p>
        <p>Below are the details of your offer:</p>

        <ul>
            <li>Start Date: {{ $data['start_date'] }}</li>
            <li>Salary: {{ $data['salary'] }}</li>
            <li>Location: {{ $data['location'] }}</li>
        </ul>

        <h2>Job Responsibilities</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris accumsan, purus non varius tincidunt, orci massa interdum metus, vitae ultrices lacus magna non eros.</p>

        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
    </div>
    <div class="content">
        <h1>Welcome {{ $data['candidate_name'] }}!</h1>
        <p>We are delighted to offer you the position of <strong>{{ $data['position'] }}</strong> at <strong>{{ $data['company_name'] }}</strong>.</p>
        <p>Below are the details of your offer:</p>

        <ul>
            <li>Start Date: {{ $data['start_date'] }}</li>
            <li>Salary: {{ $data['salary'] }}</li>
            <li>Location: {{ $data['location'] }}</li>
        </ul>

        <h2>Job Responsibilities</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris accumsan, purus non varius tincidunt, orci massa interdum metus, vitae ultrices lacus magna non eros.</p>

        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
        <h2>Additional Information</h2>
        <p>Please sign and return this offer letter by {{ $data['acceptance_deadline'] }} to confirm your acceptance.</p>
        <p>If you have any questions, feel free to contact us at {{ $data['contact_email'] }}.</p>
    </div>
    <div class="footer">
        Best regards, <br>
        {{ $data['company_name'] }} Team
    </div>
</body>
</html>
