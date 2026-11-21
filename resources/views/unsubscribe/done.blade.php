<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed</title>
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 40px 16px; color: #0f172a; }
        .card { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 36px; box-shadow: 0 4px 12px rgba(0,0,0,.08); text-align: center; }
        .check { font-size: 48px; }
        h1 { font-size: 22px; margin: 12px 0 8px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; }
        .email { font-weight: 600; color: #0f172a; word-break: break-all; }
    </style>
</head>
<body>
    <div class="card">
        <div class="check">&#9989;</div>
        <h1>You've been unsubscribed</h1>
        <p><span class="email">{{ $email }}</span> will no longer receive bulk emails from us. We're sorry to see you go.</p>
    </div>
</body>
</html>
