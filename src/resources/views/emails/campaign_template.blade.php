<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>{{ $campaign->title }}</h2>
    <div style="padding: 20px; border: 1px solid #eee;">
        {!! nl2br(e($campaign->body)) !!}
    </div>
    <p style="font-size: 12px; color: #777;">Đây là email tự động từ hệ thống.</p>
</body>
</html>