<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmation</title>
</head>

<body style="margin:0;padding:24px;background:whitesmoke;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:dimgray;line-height:1.5;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
            <td align="center">
                <div style="max-width:600px;width:100%;background:white;border:1px solid lightgray;border-radius:10px;padding:32px;">

                    <h1 style="margin:0 0 20px 0;font-size:24px;line-height:1.3;color:black;font-weight:700;text-align:center;">
                        Appointment Confirmed
                    </h1>

                    <p style="margin:8px 0 16px 0;font-size:16px;text-align:center;color:gray;">
                        Hello {{ $user->name }},
                    </p>

                    <p style="margin:8px 0 24px 0;font-size:15px;text-align:center;color:gray;">
                        Your appointment has been successfully confirmed. Please find the details below:
                    </p>

                    <div style="background:ghostwhite;border:1px solid lightgray;border-radius:8px;padding:16px 20px;margin:24px 0;text-align:left;">
                        <p style="margin:6px 0;font-size:15px;color:black;">
                            <strong>Patient:</strong> <span style="color:dimgray;">{{ $patient->name ?? '—' }}</span>
                        </p>
                        <p style="margin:6px 0;font-size:15px;color:black;">
                            <strong>Doctor:</strong> <span style="color:dimgray;">{{ $doctor->name ?? '—' }}</span>
                        </p>
                        <p style="margin:6px 0;font-size:15px;color:black;">
                            <strong>Clinic:</strong> <span style="color:dimgray;">{{ $clinic->name ?? '—' }}</span>
                        </p>
                        <p style="margin:6px 0;font-size:15px;color:black;">
                            <strong>Date:</strong> <span style="color:dimgray;">{{ $startDate ?? '—' }}</span>
                        </p>
                    </div>

                    <p style="margin:16px 0 8px 0;text-align:center;font-size:15px;color:gray;">
                        Thank you
                    </p>

                    <hr style="border:none;border-top:1px solid lightgray;margin:24px 0;" />
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
