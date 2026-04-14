<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống - QUANG CRM</title>
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
                        <h3 class="fw-bold text-primary">Manager Notication</h3>
                        <p class="text-muted">Đăng nhập để quản lý Campaign</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger p-2 small">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu</label>
                            <input type="password" name="password" class="form-control" placeholder="********" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                       <div class="d-flex justify-content-between mt-2 mb-3">
    <a href="{{ route('password.request') }}" class="text-decoration-none small">
        Quên mật khẩu?
    </a>
    <a href="{{ route('password.quick_change') }}" class="text-decoration-none small">
    Đổi mật khẩu
</a>
</div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold">ĐĂNG NHẬP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<div id="session-data" 
     data-status="{{ session('status') }}" 
     data-error="{{ $errors->first('error') }}" 
     style="display: none;">
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataEl = document.getElementById('session-data');
        const successMsg = dataEl.getAttribute('data-success');
        const errorMsg = dataEl.getAttribute('data-error');

        if (successMsg) {
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: successMsg,
                confirmButtonColor: '#0d6efd',
                timer: 3000
            });
        }

        if (errorMsg) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: errorMsg,
                confirmButtonColor: '#d33'
            });
        }
    });
</script>