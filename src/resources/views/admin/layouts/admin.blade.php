<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-md-2 sidebar shadow d-flex flex-column justify-content-between">
                <div>
                   <h4 class="text-center text-primary fw-bold mb-4" data-lang="sidebar.title"></h4>
                    <a href="/admin/dashboard" data-lang="sidebar.dashboard"></a>
                    <a href="/admin/campaign-scheduling" data-lang="sidebar.schedule"></a>
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
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        function applyLanguage(lang) {
            $('[data-lang]').each(function () {
                let key = $(this).data('lang');

                let keys = key.split('.');
                let value = lang;

                keys.forEach(k => {
                    value = value[k];
                });

                if (value) {
                    $(this).text(value);
                }
            });
        }

        function loadInitialLang() {
            let locale = $('#languageSwitcher').val();

            $.ajax({
                url: "{{ route('locale.update') }}",
                method: "PUT",
                data: {
                    locale: locale,
                    withAdminPayload: true,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    applyLanguage(response.lang);
                }
            });
        }

        $(document).ready(function () {
            loadInitialLang();

            $('#languageSwitcher').on('change', function () {
                let locale = $(this).val();

                $.ajax({
                    url: "{{ route('locale.update') }}",
                    method: "PUT",
                    data: {
                        locale: locale,
                        withAdminPayload: true,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $('#campaignTable').html(response.table);
                        applyLanguage(response.lang);
                        location.reload();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>