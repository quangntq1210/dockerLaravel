<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>{{ $campaign->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
        }

        h1 {
            color: #333;
            font-size: 22px;
        }

        p {
            color: #555;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>{{ $campaign->title }}</h1>
        <p>{{ __('message.hello') }}, <strong>{{ $subscriber->name }}</strong>!</p>
        <p>{{ $campaign->body }}</p>
        <div class="footer">
            <p>{{ __('message.email_sent_auto') }}</p>
        </div>
    </div>
</body>

</html>