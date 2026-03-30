<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUANG CRM - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; color: white; padding-top: 20px; }
        .sidebar a { color: #adb5bd; text-decoration: none; padding: 10px 20px; display: block; }
        .sidebar a:hover { background: #343a40; color: white; }
        .card-stat { border: none; border-radius: 10px; transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar shadow">
                <h4 class="text-center text-primary fw-bold mb-4">QUANG CRM</h4>
                <a href="/admin/dashboard">📊 Dashboard</a>
                <a href="/admin/campaigns">📧 Chiến dịch</a>
                <a href="/admin/subscribers">👥 Người đăng ký</a>
                <hr>
                <form action="/logout" method="POST" class="px-3">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm w-100">Đăng xuất</button>
                </form>
            </div>

            <div class="col-md-10 p-4">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>