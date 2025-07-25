<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #8b5cf6;
        }
        .email-logo {
            font-size: 24px;
            font-weight: bold;
            color: #8b5cf6;
            margin-bottom: 10px;
        }
        .email-content {
            font-size: 16px;
            line-height: 1.8;
        }
        .email-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 14px;
            color: #6b7280;
        }
        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="email-logo">📧 BulkMailer</div>
            <p style="margin: 0; color: #6b7280;">Professional Email Marketing</p>
        </div>

        <div class="email-content">
            {!! $emailBody !!}
        </div>

        <div class="email-footer">
            <p>This email was sent using BulkMailer - Professional Email Marketing Solution</p>
            <p style="margin: 5px 0 0 0;">© {{ date('Y') }} BulkMailer. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
