<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('message.notification_management') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-light">
    {{-- Navigation Bar --}}
    <nav class="navbar navbar-dark bg-dark px-4">
        <span class="navbar-brand fw-bold mb-0">{{ __('message.notification_management') }}</span>
    
        <div class="d-flex align-items-center gap-3 ms-auto flex-nowrap">
            <div class="language-switcher">
                <select id="languageSwitcher" class="form-select form-select-sm" style="width: auto; min-width: 9rem;">
                    <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>
                        {{ __('message.vi') }}
                    </option>
                    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                        {{ __('message.en') }}
                    </option>
                </select>
            </div>
            <span class="text-white text-nowrap">{{ auth()->user()->name }}</span>
            <form action="/logout" method="POST" class="d-flex mb-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">{{ __('message.logout') }}</button>
            </form>
        </div>
    </nav>

    {{-- Content --}}
    <div class="container py-4">
        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/helpers/renderPagination.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#languageSwitcher').on('change', function () {
                var locale = $(this).val();
        
                $.ajax({
                    url: @js(route('locale.update')),
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({ locale: locale }),
                    success: function () {
                        location.reload();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
