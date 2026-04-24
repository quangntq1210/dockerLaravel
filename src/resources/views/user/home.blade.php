@extends('user.layouts.user')

@section('content')
    @php
        $subscribedMap = collect($subscribedCampaignIds ?? [])->flip();
    @endphp

    <div class="home-campaign-page">
        <div class="home-campaign-header">
            <h1 class="home-campaign-title">{{ __('message.campaigns') }}</h1>
            <p class="home-campaign-subtitle">
                @auth
                    {{ __('message.select_campaigns_hint') }}
                @else
                    {{ __('message.enter_your_info_to_subscribe_unsubscribe_without_login') }}
                @endauth
            </p>
        </div>

        @if ($campaigns->isEmpty())
            <div class="dashboard-card">
                <div class="dashboard-card-body text-center py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                    <span class="text-muted">{{ __('message.no_campaigns_available') }}</span>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($campaigns as $campaign)
                    @php $isSubscribed = $subscribedMap->has($campaign->id); @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="campaign-card campaign-detail-trigger" id="campaign-card-{{ $campaign->id }}"
                            data-campaign-title="{{ $campaign->title }}" data-campaign-body="{{ $campaign->body }}"
                            data-campaign-status="{{ $isSubscribed ? __('message.subscribed') : __('message.not_subscribed') }}">
                            <div class="campaign-card-image-wrap">
                                <div class="campaign-card-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>
                            </div>

                            <div class="campaign-card-body">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                    <h3 class="campaign-card-title mb-0">{{ $campaign->title }}</h3>
                                    @auth
                                        <span
                                            class="badge campaign-status-badge {{ $isSubscribed ? 'campaign-status-subscribed' : 'campaign-status-unsubscribed' }}"
                                            data-role="status">
                                            {{ $isSubscribed ? __('message.subscribed') : __('message.not_subscribed') }}
                                        </span>
                                    @endauth
                                </div>

                                <p class="campaign-card-description">
                                    {{ $campaign->body }}
                                </p>

                                @auth
                                    <button type="button"
                                        class="btn campaign-action-btn mt-auto {{ $isSubscribed ? 'btn-outline-secondary' : 'btn-primary' }}"
                                        data-campaign-id="{{ $campaign->id }}"
                                        data-subscribed="{{ $isSubscribed ? '1' : '0' }}">
                                        <span class="campaign-action-label">
                                            {{ $isSubscribed ? __('message.unsubscribe') : __('message.subscribe') }}
                                        </span>
                                    </button>
                                @else
                                    <button type="button"
                                        class="btn btn-primary campaign-action-btn mt-auto guest-subscription-trigger"
                                        data-campaign-id="{{ $campaign->id }}">
                                        {{ __('message.subscribe') }} / {{ __('message.unsubscribe') }}
                                    </button>
                                @endauth
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="home-campaign-pagination mt-4">
                {{ $campaigns->links() }}
            </div>

            @guest
                <div class="modal fade" id="guestSubscribeModal" tabindex="-1" aria-labelledby="guestSubscribeModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content campaign-detail-modal-content">
                            <form id="guest-subscribe-form" novalidate>
                                <div class="modal-header">
                                    <h5 class="modal-title" id="guestSubscribeModalLabel">{{ __('message.subscribe') }} /
                                        {{ __('message.unsubscribe') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="guest-campaign-id" name="campaign_id">
                                    <div class="mb-3">
                                        <label for="guest-name" class="form-label">{{ __('message.name') }}</label>
                                        <input type="text" id="guest-name" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="guest-email" class="form-label">{{ __('message.email') }}</label>
                                        <input type="email" id="guest-email" name="email" class="form-control" required>
                                    </div>
                                    <div class="mb-0">
                                        <label for="guest-action" class="form-label">{{ __('message.action') }}</label>
                                        <select id="guest-action" name="action" class="form-select">
                                            <option value="subscribe">{{ __('message.subscribe') }}</option>
                                            <option value="unsubscribe">{{ __('message.unsubscribe') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light"
                                        data-bs-dismiss="modal">{{ __('message.close') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('message.submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest

            <div class="modal fade" id="campaignDetailModal" tabindex="-1" aria-labelledby="campaignDetailModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content campaign-detail-modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="campaignDetailModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0" id="campaignDetailModalBody"></p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @guest
        <script>
            $(function() {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                const guestModalEl = document.getElementById('guestSubscribeModal');
                const guestModal = guestModalEl ? new bootstrap.Modal(guestModalEl) : null;

                function showActionToast(icon, text) {
                    if (typeof Swal === 'undefined') return;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: icon,
                        title: text,
                        showConfirmButton: false,
                        timer: 2200,
                        timerProgressBar: true
                    });
                }

                $(document).on('click', '.guest-subscription-trigger', function() {
                    $('#guest-campaign-id').val($(this).data('campaign-id'));
                    if (guestModal) guestModal.show();
                });

                $('#guest-subscribe-form').on('submit', function(e) {
                    e.preventDefault();
                    const campaignId = $('#guest-campaign-id').val();
                    const name = $('#guest-name').val().trim();
                    const email = $('#guest-email').val().trim();
                    const action = $('#guest-action').val();

                    const method = action === 'unsubscribe' ? 'DELETE' : 'POST';
                    const url = action === 'unsubscribe' ? @js(route('home.destroy')) :
                        @js(route('home.store'));
                    const payload = {
                        name: name,
                        email: email,
                        campaign_ids: [campaignId]
                    };

                    $.ajax({
                        url: url,
                        method: method,
                        data: {
                            ...payload,
                            _token: csrfToken
                        },
                        success: function(response) {
                            if (guestModal) guestModal.hide();
                            showActionToast('success', response?.message || (action ===
                                'unsubscribe' ?
                                'Unsubscribed successfully.' : 'Subscribed successfully.'));
                        },
                        error: function(xhr) {
                            showActionToast('error', xhr?.responseJSON?.message ||
                                @js(__('message.error_occurred')));
                        }
                    });
                });
            });
        </script>
    @endguest

    @auth
        <script>
            $(function() {
                const subscribeUrl = @js(route('campaigns.recipients.store.bulk'));
                const unsubscribeUrlTemplate = @js(route('campaigns.recipients.destroy', ['campaignId' => '__ID__']));
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                const modalEl = document.getElementById('campaignDetailModal');
                const modalInstance = modalEl ? new bootstrap.Modal(modalEl) : null;
                const subscribeSuccessText = @js(__('message.subscribe_success'));
                const unsubscribeSuccessText = @js(__('message.unsubscribe_success'));
                const genericErrorText = @js(__('message.error_occurred'));

                function showActionToast(icon, text) {
                    if (typeof Swal === 'undefined') return;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: icon,
                        title: text,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }

                $(document).on('click', '.campaign-detail-trigger', function(e) {
                    if ($(e.target).closest('.campaign-action-btn, a, button').length) {
                        return;
                    }

                    if (!modalInstance) {
                        return;
                    }

                    const $card = $(this);
                    $('#campaignDetailModalLabel').text($card.data('campaign-title') || '');
                    $('#campaignDetailModalBody').text($card.data('campaign-body') || '');
                    modalInstance.show();
                });

                $(document).on('click', '.campaign-action-btn[data-campaign-id]', function() {
                    const $button = $(this);
                    const campaignId = $button.data('campaign-id');
                    const subscribed = String($button.data('subscribed')) === '1';
                    const requestConfig = subscribed ? {
                        url: unsubscribeUrlTemplate.replace('__ID__', campaignId),
                        method: 'DELETE',
                        data: {
                            _token: csrfToken
                        }
                    } : {
                        url: subscribeUrl,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            campaigns: [campaignId]
                        }),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    };

                    $button.prop('disabled', true);

                    $.ajax(requestConfig)
                        .done(function(response) {
                            const nowSubscribed = !subscribed;
                            const $card = $('#campaign-card-' + campaignId);
                            const $status = $card.find('[data-role="status"]');
                            const $label = $button.find('.campaign-action-label');

                            $button.data('subscribed', nowSubscribed ? '1' : '0');
                            $button.toggleClass('btn-primary', !nowSubscribed);
                            $button.toggleClass('btn-outline-secondary', nowSubscribed);

                            $status
                                .toggleClass('campaign-status-subscribed', nowSubscribed)
                                .toggleClass('campaign-status-unsubscribed', !nowSubscribed)
                                .text(nowSubscribed ? @js(__('message.subscribed')) :
                                    @js(__('message.not_subscribed')));
                            $card.attr('data-campaign-status', nowSubscribed ?
                                @js(__('message.subscribed')) :
                                @js(__('message.not_subscribed')));

                            $label.text(nowSubscribed ? @js(__('message.unsubscribe')) :
                                @js(__('message.subscribe')));
                            showActionToast('success', response?.message || (nowSubscribed ?
                                subscribeSuccessText :
                                unsubscribeSuccessText));
                        })
                        .fail(function(xhr) {
                            showActionToast('error', xhr?.responseJSON?.message || genericErrorText);
                        })
                        .always(function() {
                            $button.prop('disabled', false);
                        });
                });
            });
        </script>
    @endauth
@endpush
