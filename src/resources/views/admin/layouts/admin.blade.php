<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>CRM - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

=======
    <title>QUANG CRM - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: #212529;
            color: white;
            padding-top: 20px;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 20px;
            display: block;
        }

        .sidebar a:hover {
            background: #343a40;
            color: white;
        }

        .card-stat {
            border: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        .card-stat:hover {
            transform: translateY(-5px);
        }
    </style>
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
</head>

<body>
    <div class="container-fluid">
<<<<<<< HEAD
        <div class="row min-vh-100">
            <div class="col-md-2 sidebar shadow d-flex flex-column justify-content-between">
                <div>
                   <h4 class="text-center text-primary fw-bold mb-4" data-lang="sidebar.title"></h4>
                    <a href="/admin/dashboard" data-lang="sidebar.dashboard"></a>
                    <a href="/admin/campaigns" data-lang="sidebar.schedule"></a>
                   <a href="/admin/subscribers" data-lang="sidebar.subscriber"></a>
                    <hr>
                    <form action="/logout" method="POST" class="px-3">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm w-100" data-lang="sidebar.logout"></button>
                    </form>
                    
                <div class="language-switcher">
                    <select id="languageSwitcher" class="form-select">
                      
                        <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>
                             <i class="fas fa-mouse-pointer"></i> 🇻🇳 Tiếng Việt
                        </option>
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                            <i class="fas fa-mouse-pointer"></i> 🇺🇸 English
                        </option>
                    </select>
                </div>
            </div>
                </div>

          
=======
        <div class="row">
            <div class="col-md-2 sidebar shadow">
                <h4 class="text-center text-primary fw-bold mb-4">QUANG CRM</h4>
                <a href="/admin/dashboard">Dashboard</a>
                <a href="/admin/campaign-scheduling">Lịch gửi thông báo</a>
                <a href="/admin/subscribers">Người đăng ký</a>
                <hr>
                <form action="/logout" method="POST" class="px-3">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm w-100">Đăng xuất</button>
                </form>
            </div>
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e

            <div class="col-md-10 p-4">
                @yield('content')
            </div>
        </div>
    </div>
<<<<<<< HEAD
    @stack('scripts')
</body>
</html>
=======
</body>

</html>
>>>>>>> d6edc5e93a1341eca53919208a0412602627170e
