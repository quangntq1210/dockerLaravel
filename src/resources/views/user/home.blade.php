<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('message.subscribe_title') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --primary-light: #e0e7ff;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Nunito', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .subscribe-card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 600px;
            overflow: hidden;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            padding: 2.5rem 2.5rem 2rem;
            color: #fff;
        }
        .card-header-custom .icon-wrap {
            width: 56px;
            height: 56px;
            background: rgba(255,255,255,0.2);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1rem;
        }
        .card-header-custom h1 {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 .4rem;
        }
        .card-header-custom p {
            opacity: .85;
            margin: 0;
            font-size: .95rem;
            line-height: 1.5;
        }
        .card-body-custom {
            padding: 2rem 2.5rem 2.5rem;
        }
        .form-label-custom {
            font-weight: 700;
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #374151;
            margin-bottom: .4rem;
        }
        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap .bi {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
        }
        .input-icon-wrap .form-control {
            padding-left: 2.4rem;
        }
        .form-control {
            border: 1.5px solid #e5e7eb;
            border-radius: .75rem;
            padding: .7rem 1rem;
            font-size: .95rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .campaign-list {
            border: 1.5px solid #e5e7eb;
            border-radius: .75rem;
            max-height: 260px;
            overflow-y: auto;
            padding: .5rem 0;
            background: #fafafa;
        }
        .campaign-list:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .campaign-item {
            display: flex;
            align-items: flex-start;
            gap: .8rem;
            padding: .7rem 1rem;
            cursor: pointer;
            transition: background .15s;
            border-bottom: 1px solid #f3f4f6;
        }
        .campaign-item:last-child {
            border-bottom: none;
        }
        .campaign-item:hover {
            background: #f0f4ff;
        }
        .campaign-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: .35rem;
            border: 2px solid #d1d5db;
            accent-color: var(--primary);
            flex-shrink: 0;
            margin-top: 2px;
            cursor: pointer;
        }
        .campaign-item input:checked ~ .campaign-text .campaign-title {
            color: var(--primary);
        }
        .campaign-title {
            font-weight: 600;
            font-size: .9rem;
            color: #111827;
            transition: color .15s;
        }
        .campaign-body-text {
            font-size: .8rem;
            color: #6b7280;
            margin-top: .1rem;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .campaign-search {
            border: none;
            border-bottom: 1.5px solid #e5e7eb;
            border-radius: 0;
            background: #fff;
            padding: .6rem 1rem .6rem 2.4rem;
            font-size: .9rem;
        }
        .campaign-search:focus {
            border-color: var(--primary);
            box-shadow: none;
            background: #fff;
        }
        .selected-count-badge {
            background: var(--primary-light);
            color: var(--primary);
            font-size: .78rem;
            font-weight: 700;
            padding: .15rem .55rem;
            border-radius: 999px;
        }
        .btn-subscribe {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            border: none;
            border-radius: .75rem;
            padding: .8rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            width: 100%;
            transition: opacity .2s, transform .15s;
            letter-spacing: .02em;
        }
        .btn-subscribe:hover:not(:disabled) {
            opacity: .93;
            transform: translateY(-1px);
        }
        .btn-subscribe:disabled {
            opacity: .7;
            cursor: not-allowed;
        }
        .toast-wrap {
            position: fixed;
            top: 1.2rem;
            right: 1.2rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }
        .divider {
            border: none;
            border-top: 1.5px solid #f3f4f6;
            margin: 1.5rem 0;
        }
        @media (max-width: 480px) {
            .card-header-custom, .card-body-custom { padding-left: 1.3rem; padding-right: 1.3rem; }
        }
    </style>
</head>
<body>

{{-- Toast container --}}
<div class="toast-wrap" id="toast-container"></div>

<div class="subscribe-card">
    {{-- Header --}}
    <div class="card-header-custom">
        <div class="icon-wrap"><i class="bi bi-megaphone-fill"></i></div>
        <h1>{{ __('message.subscribe_title') }}</h1>
        <p>{{ __('message.subscribe_subtitle') }}</p>
    </div>

    {{-- Body --}}
    <div class="card-body-custom">
        <form id="subscribe-form" novalidate>
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label class="form-label-custom">{{ __('message.your_name') }}</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-person"></i>
                    <input type="text" id="name" name="name" class="form-control"
                        placeholder="{{ __('message.your_name') }}" required>
                </div>
                <div class="invalid-feedback d-block" id="err-name"></div>
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="form-label-custom">{{ __('message.your_email') }}</label>
                <div class="input-icon-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control"
                        placeholder="{{ __('message.your_email') }}" required>
                </div>
                <div class="invalid-feedback d-block" id="err-email"></div>
            </div>

            <hr class="divider">

            {{-- Campaigns --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label-custom mb-0">{{ __('message.select_campaigns') }}</label>
                    <span class="selected-count-badge" id="selected-count">0 {{ __('message.selected') }}</span>
                </div>
                <p class="text-muted mb-2" style="font-size:.82rem">
                    <i class="bi bi-info-circle me-1"></i>{{ __('message.select_campaigns_hint') }}
                </p>

                @if($campaigns->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        {{ __('message.no_campaigns_available') }}
                    </div>
                @else
                    <div class="campaign-list">
                        {{-- Search --}}
                        <div class="input-icon-wrap">
                            <i class="bi bi-search" style="top:50%; z-index:1"></i>
                            <input type="text" id="campaign-search" class="form-control campaign-search"
                                placeholder="{{ __('message.search') }}...">
                        </div>
                        {{-- List --}}
                        <div id="campaign-items">
                            @foreach($campaigns->items() as $campaign)
                            <label class="campaign-item" id="campaign-wrap-{{ $campaign->id }}">
                                <input type="checkbox" name="campaign_ids[]" value="{{ $campaign->id }}"
                                    class="campaign-checkbox">
                                <div class="campaign-text">
                                    <div class="campaign-title">{{ $campaign->title }}</div>
                                    <div class="campaign-body-text">{{ $campaign->body }}</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="invalid-feedback d-block" id="err-campaigns"></div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-subscribe" id="btn-submit">
                <span id="btn-text">
                    <i class="bi bi-send me-2"></i>{{ __('message.subscribe_btn') }}
                </span>
                <span id="btn-loading" class="d-none">
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ __('message.subscribing') }}
                </span>
            </button>
        </form>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
$(function () {
    // Campaign search filter
    $('#campaign-search').on('input', function () {
        const q = $(this).val().toLowerCase();
        $('#campaign-items .campaign-item').each(function () {
            const title = $(this).find('.campaign-title').text().toLowerCase();
            $(this).toggle(title.includes(q));
        });
    });

    // Selected count badge
    $(document).on('change', '.campaign-checkbox', function () {
        const count = $('.campaign-checkbox:checked').length;
        $('#selected-count').text(count + ' {{ __("message.selected") }}');
        if (count > 0) $('#err-campaigns').text('');
    });

    // Form submit
    $('#subscribe-form').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const campaignIds = $('.campaign-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        let valid = true;
        if (!name) { showFieldError('err-name', '{{ __("validation.required", ["attribute" => __("message.your_name")]) }}'); valid = false; }
        if (!email || !isValidEmail(email)) { showFieldError('err-email', '{{ __("validation.email", ["attribute" => __("message.your_email")]) }}'); valid = false; }
        if (campaignIds.length === 0) { showFieldError('err-campaigns', '{{ __("message.please_select_at_least_one_campaign") }}'); valid = false; }
        if (!valid) return;

        setLoading(true);

        $.ajax({
            url: '{{ route("home.store") }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { name, email, campaign_ids: campaignIds },
            success: function (res) {
                showToast(res.message, 'success');
                $('#subscribe-form')[0].reset();
                $('#selected-count').text('0 {{ __("message.selected") }}');
            },
            error: function (res) {
                const errors = res?.responseJSON?.errors;
                if (errors) {
                    if (errors.name) showFieldError('err-name', errors.name[0]);
                    if (errors.email) showFieldError('err-email', errors.email[0]);
                    if (errors.campaign_ids) showFieldError('err-campaigns', errors.campaign_ids[0]);
                } else {
                    showToast(res?.responseJSON?.message || '{{ __("message.subscribe_error") }}', 'danger');
                }
            },
            complete: function () { setLoading(false); }
        });
    });

    function clearErrors() {
        $('#err-name, #err-email, #err-campaigns').text('');
    }

    function showFieldError(id, msg) {
        $('#' + id).text(msg);
    }

    function setLoading(on) {
        $('#btn-submit').prop('disabled', on);
        $('#btn-text').toggleClass('d-none', on);
        $('#btn-loading').toggleClass('d-none', !on);
    }

    function isValidEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
    }

    function showToast(message, type) {
        const id = 'toast-' + Date.now();
        const icons = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-circle-fill' };
        const icon = icons[type] || 'bi-info-circle-fill';
        const html = `
            <div id="${id}" class="toast align-items-center text-white bg-${type} border-0 show mb-2"
                 role="alert" style="min-width:280px; border-radius:.75rem; box-shadow:0 4px 20px rgba(0,0,0,.15)">
                <div class="d-flex p-3 gap-2 align-items-center">
                    <i class="bi ${icon} fs-5 flex-shrink-0"></i>
                    <div class="flex-grow-1" style="font-size:.9rem">${message}</div>
                    <button type="button" class="btn-close btn-close-white flex-shrink-0" data-bs-dismiss="toast"></button>
                </div>
            </div>`;
        $('#toast-container').append(html);
        const el = document.getElementById(id);
        const t = new bootstrap.Toast(el, { delay: 4000, autohide: true });
        t.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
});
</script>
</body>
</html>
