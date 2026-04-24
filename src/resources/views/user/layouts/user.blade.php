<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('message.notification_management') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
</head>

<body class="user-dashboard-body">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top app-navbar">
        <div class="container-fluid app-navbar-container">
            <a class="navbar-brand app-brand" href="{{ url('/') }}">
                {{ __('message.notification_management') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbarContent"
                aria-controls="userNavbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="userNavbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 app-navbar-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                            {{ __('message.home') }}
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 app-navbar-actions">
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm app-action-btn dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate me-1"></i>
                            {{ app()->getLocale() === 'vi' ? __('message.vi') : __('message.en') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="{{ route('locale.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="locale" value="en">
                                    <button type="submit" class="dropdown-item">
                                        {{ __('message.en') }}
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('locale.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="locale" value="vi">
                                    <button type="submit" class="dropdown-item">
                                        {{ __('message.vi') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>

                    @auth
                        <a href="{{ url('/notifications') }}"
                            class="btn btn-light btn-sm app-icon-btn {{ request()->is('notifications') ? 'active' : '' }}"
                            aria-label="Notifications">
                            <i class="bi bi-bell"></i>
                        </a>

                        <div class="dropdown">
                            <button class="btn app-avatar-dropdown-toggle p-0 border-0 bg-transparent" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false" aria-label="User menu">
                                @if (!empty(auth()->user()->avatar_display_url))
                                    <img src="{{ auth()->user()->avatar_display_url }}" alt="Avatar"
                                        class="app-avatar app-avatar-image">
                                @else
                                    <span class="app-avatar">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end app-user-dropdown-menu">
                                <li>
                                    <a class="dropdown-item app-user-menu-item" href="{{ url('/profile') }}">
                                        <i class="bi bi-person app-user-menu-icon"></i>{{ __('message.profile') }}
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="js-logout-form mb-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger app-user-menu-item">
                                            <i class="bi bi-box-arrow-right app-user-menu-icon"></i>{{ __('message.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                            {{ __('message.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container dashboard-main-content">
        @yield('content')
    </div>

    <script>
        window.timeAgoI18n = {
            locale: @js(app()->getLocale()),
            just_now: @js(__('message.just_now')),
            minutes_ago: @js(__('message.minutes_ago')),
            hours_ago: @js(__('message.hours_ago')),
            days_ago: @js(__('message.days_ago')),
        };
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/helpers/renderPagination.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('.js-logout-form');
            if (!logoutForms.length) return;

            logoutForms.forEach(function(logoutForm) {
                logoutForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

                    try {
                        const response = await fetch(logoutForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            window.location.reload();
                            return;
                        }
                    } catch (error) {
                        // fallback below
                    }

                    logoutForm.submit();
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
