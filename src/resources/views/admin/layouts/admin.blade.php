<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manager CRM - Admin</title>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.x.x/css/all.css">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <!-- <style>
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
    </style> -->

    @stack('styles')
</head>

<body>
<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar d-flex flex-column justify-content-between">

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
                 <div class="p-3">
                <select id="languageSwitcher" class="form-select">
                    <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>
                        🇻🇳 Tiếng Việt
                    </option>
                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                        🇺🇸 English
                    </option>
                </select>
            </div>
            </div>
        </div>

        <div class="col-md-10 p-4">
            @yield('content')
        </div>

    </div>
</div>

@stack('scripts')
</body>
</html>