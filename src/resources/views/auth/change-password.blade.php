<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu nhanh - QUANG CRM</title>
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
                        <h3 class="fw-bold text-primary">Đổi Mật Khẩu</h3>
                        <p class="text-muted">Không cần đăng nhập</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger p-2 small">
                            {{ $errors->first() }}
                        </div>
                    @endif

               <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Email tài khoản</label>
                            <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu cũ (Hiện tại)</label>
                            <input type="password" name="current_password" class="form-control" placeholder="********" required>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu mới" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">XÁC NHẬN ĐỔI MẬT KHẨU</button>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}" class="text-decoration-none small text-secondary">Quay lại Đăng nhập</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>