<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chào mừng</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        p { color: #555; line-height: 1.6; }
        .footer { margin-top: 30px; font-size: 12px; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Xin chào, {{ $name }}!</h1>
        <p>Đây là email được gửi tự động thông qua <strong>Laravel Queue</strong>.</p>
        <p>Email này được xử lý bất đồng bộ bởi queue worker, giúp ứng dụng không bị chậm khi gửi mail.</p>
        <div class="footer">
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>
