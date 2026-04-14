<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-md-2 sidebar shadow d-flex flex-column justify-content-between">
                <div>
                    <div>
    <h4 class="text-center text-primary fw-bold mb-4" data-lang="sidebar.title">
        {{ __('sidebar.title') }}
    </h4>
    

    <a href="{{ route('admin.dashboard') }}" 
       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-primary text-white' : '' }}" 
       data-lang="sidebar.dashboard">
        <i class="fas fa-tachometer-alt me-2"></i> {{ __('sidebar.dashboard') }}
    </a>

    <a href="{{ route('admin.campaign-scheduling') }}" 
       class="nav-link {{ request()->is('admin/campaign-scheduling*') ? 'active bg-primary text-white' : '' }}" 
       data-lang="sidebar.schedule">
        <i class="fas fa-calendar-alt me-2"></i> {{ __('sidebar.schedule') }}
    </a>

  <a href="{{ route('admin.users.index') }}" 
   class="nav-link {{ request()->routeIs('admin.users.*') ? 'active bg-primary text-white' : '' }}" 
   data-lang="sidebar.user_manager">
    
    <i class="fas fa-users-cog me-2"></i> 
    {{ __('sidebar.user_manager') }}
</a>

    <hr>
    </div>
                 <hr>   
                    <form action="/logout" method="POST" class="px-3">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm w-100" data-lang="sidebar.logout">
                            {{ __('sidebar.logout') }}
                        </button>
                    </form>
                </div>
                
                <div class="language-switcher p-3">
                    <select id="languageSwitcher" class="form-select">
                        <option value="vi" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>🇻🇳 Tiếng Việt</option>
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                    </select>
                </div>
            </div>

            <div class="col-md-10 p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        const CRM_Admin = {
            currentLangData: null,
            applyLanguage: function(langData) {
                if (!langData) return;
                this.currentLangData = langData;
                $('[data-lang]').each(function() {
                    const $el = $(this);
                    const key = $el.data('lang');
                    const value = key.split('.').reduce((obj, i) => (obj ? obj[i] : null), langData);
                    if (value) {
                        if ($el.is('input, textarea')) {
                            $el.attr('placeholder', value);
                        } else {
                            $el.text(value);
                        }
                    }
                });
            },
           fetchDashboardData: function(url) {
    $.ajax({
        url: url,
        method: "GET",
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function(response) {
  
            if (response.table) { $('#campaignTable').html(response.table); }
            if (response.stats) {
                $('#total-campaigns').text(response.stats.total_campaigns);
                $('#total-subscribers').text(response.stats.total_subscribers);
            }

            if (CRM_Admin.currentLangData) { 
                CRM_Admin.applyLanguage(CRM_Admin.currentLangData); 
            }
            if (typeof fetchUsers === 'function') {
                fetchUsers(currentPage);
            }
        },
        error: function() { window.location.href = url; }
    });
}
        };

    $(document).ready(function() {
    $('#languageSwitcher').on('change', function() {
        const locale = $(this).val();
        
        $.ajax({
            url: "{{ route('locale.update') }}",
            method: "PUT",
            data: { 
                locale: locale, 
                _token: "{{ csrf_token() }}" 
            },
            success: function(response) {
    
                CRM_Admin.currentLangData = response.lang;
                
 
                CRM_Admin.applyLanguage(response.lang);
  
                if (typeof fetchUsers === 'function') {
              
                    fetchUsers(currentPage); 
                } else {
               
                    let activePageUrl = $('.pagination .active a').attr('href');
                    CRM_Admin.fetchDashboardData(activePageUrl || "{{ route('admin.dashboard') }}");
                }

                Swal.fire({
                    icon: 'success',
                    title: locale === 'vi' ? 'Đã đổi ngôn ngữ!' : 'Language changed!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            },
            error: function() {
                window.location.reload();
            }
        });
    });
});
    </script>
    @stack('scripts')
</body>
</html>