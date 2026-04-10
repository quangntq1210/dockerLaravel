<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #0d6efd; border: none; padding: 12px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">Quên mật khẩu?</h3>
                        <p class="text-muted small">Chúng tôi sẽ gửi mật khẩu mới vào email của bạn.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success p-2 small text-center mb-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    @error('email')
                        <div class="alert alert-danger p-2 small text-center mb-3">
                            {{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email đăng ký</label>
                            <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required autofocus>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">GỬI MẬT KHẨU MỚI</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-decoration-none small">Quay lại Đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>