<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('message.send_account_mail') }}</title>
</head>

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

<body>
    <div class="container">
        <h1>{{ __('message.send_account_mail') }}</h1>
        <p>{{ __('message.hello') }}, <strong>{{ $user->name }}</strong>!</p>
        <p>{{ __('message.account_mail_content', ['email' => $user->email, 'password' => env('USER_PASSWORD_DEFAULT')]) }}
        </p>
        <p>{{ __('message.login_url') }}: <a href="{{ route('login') }}">{{ route('login') }}</a></p>
        <div class="footer">
            <p>{{ __('message.email_sent_auto') }}</p>
        </div>
    </div>
</body>

</html>
