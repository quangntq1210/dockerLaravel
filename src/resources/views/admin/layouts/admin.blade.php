<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

</head>

<body>
    <div class="container-fluid">
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

          

            <div class="col-md-10 p-4">
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
