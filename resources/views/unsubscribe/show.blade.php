<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe</title>
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 40px 16px; color: #0f172a; }
        .card { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 36px; box-shadow: 0 4px 12px rgba(0,0,0,.08); text-align: center; }
        h1 { font-size: 22px; margin: 0 0 8px; }
        p { font-size: 14px; color: #475569; line-height: 1.6; }
        .email { font-weight: 600; color: #0f172a; word-break: break-all; }
        .btn { display: inline-block; margin-top: 20px; background: #dc2626; color: #fff !important; text-decoration: none; border: none; cursor: pointer; font-size: 15px; font-weight: 600; padding: 12px 32px; border-radius: 8px; }
        .btn:hover { background: #b91c1c; }
        .note { margin-top: 16px; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        @if($already)
            <h1>Already unsubscribed</h1>
            <p><span class="email">{{ $email }}</span> is already unsubscribed. You won't receive further bulk emails.</p>
        @else
            <h1>Unsubscribe from emails?</h1>
            <p>Click below to stop receiving bulk emails at<br><span class="email">{{ $email }}</span></p>
            <form method="POST" action="{{ URL::signedRoute('unsubscribe.store', ['email' => $email]) }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" class="btn">Yes, unsubscribe me</button>
            </form>
            <p class="note">This takes effect immediately. Transactional or account emails are not affected.</p>
        @endif
    </div>
</body>
</html>
