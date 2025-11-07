<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 24px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: #2c3e50;
            font-size: 22px;
            margin-bottom: 16px;
        }

        p {
            line-height: 1.6;
            margin: 8px 0;
        }

        .details {
            background: #f3f4f6;
            padding: 16px;
            border-radius: 6px;
            margin: 16px 0;
        }

        .details p {
            margin: 6px 0;
        }

        .details strong {
            display: inline-block;
            width: 130px;
        }

        .footer {
            font-size: 12px;
            color: #888;
            margin-top: 24px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Appointment Confirmed</h1>

        <p>Hello {{ $user->name }},</p>

        <p>Your appointment has been successfully confirmed. Below are the details:</p>

        <div class="details">
            <p><strong>Patient:</strong> {{ $patient->name ?? '—' }}</p>
            <p><strong>Doctor:</strong> {{ $doctor->name ?? '—' }}</p>
            <p><strong>Clinic:</strong> {{ $clinic->name ?? '—' }}</p>
            <p><strong>Date:</strong>
                {{ $appointment->start_date instanceof \Carbon\CarbonInterface
    ? $appointment->start_date->format('d/m/Y H:i')
    : $appointment->start_date }}
            </p>
        </div>

        <p>Thank you</p>
    </div>
</body>

</html>
