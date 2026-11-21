<!DOCTYPE html>
<html>
<head>
    <title>Bulk Email Sender</title>
</head>
<body>
    <p>{!! $mailData['body'] !!}</p>

    {{-- Universal unsubscribe footer — always rendered --}}
    <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0 12px;">
    <p style="font-size:12px;color:#6b7280;text-align:center;margin:0;">
        You received this email because you are subscribed to our mailing list.
        <br>
        @if(!empty($unsubscribeUrl ?? null))
            <a href="{{ $unsubscribeUrl }}" style="color:#8b5cf6;text-decoration:underline;">Unsubscribe</a>
        @else
            <a href="{{ url('/unsubscribe') }}" style="color:#8b5cf6;text-decoration:underline;">Unsubscribe</a>
        @endif
    </p>
</body>
</html>
