@extends('user.layouts.user')

@section('content')
    <div class="dashboard-page">
        <section class="dashboard-section">
            <div class="section-header">
                <h2 class="section-title">
                    {{ __('message.notification') }}
                    <span id="unread-badge" class="badge bg-danger ms-1 unread-badge-hidden"></span>
                </h2>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm icon-button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" id="btn-mark-all-read">
                                <i class="bi bi-check2-all me-2"></i>{{ __('message.notification_mark_as_read') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="nav nav-pills notification-tabs" id="notification-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="all">{{ __('message.all') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="unread">{{ __('message.unread') }}</a>
                </li>
            </ul>

            <div id="toast-container" class="toast-container-custom position-fixed top-0 end-0 p-2 d-flex flex-column gap-2">
            </div>

            <div class="dashboard-card">
                <div class="dashboard-card-body p-0">
                    <div id="notification-list">
                        <div class="text-center py-5 text-muted" id="loading-spinner">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            {{ __('message.loading') }}
                        </div>
                    </div>
                </div>
            </div>

            <nav aria-label="{{ __('message.pagination') }}" class="pagination-wrap" id="pagination-wrap">
                <ul class="pagination" id="pagination-list">
                </ul>
            </nav>
        </section>
    </div>

    <div class="modal fade" id="notificationCampaignModal" tabindex="-1" aria-labelledby="notificationCampaignModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content campaign-detail-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationCampaignModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" id="notificationCampaignModalBody"></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let FirstCount = 0;
        $(function() {
            const state = {
                notifications: {
                    page: 1,
                    filter: 'all',
                    loading: false,
                    perPage: 7
                }
            };

            const $notificationList = $('#notification-list');
            const $notificationPager = $('#pagination-list');
            const $notificationWrap = $('#pagination-wrap');
            const campaignModalEl = document.getElementById('notificationCampaignModal');
            const campaignModal = campaignModalEl ? new bootstrap.Modal(campaignModalEl) : null;
            const emptyCampaignTitle = @js(__('message.notification'));
            const emptyCampaignBody = @js(__('message.notification_empty'));

            // Load notifications
            loadNotifications(1, state.notifications.filter);

            // Change tab
            $('#notification-tabs .nav-link').on('click', function(e) {
                e.preventDefault();
                $('#notification-tabs .nav-link').removeClass('active');
                $(this).addClass('active');

                state.notifications.filter = $(this).data('filter');
                state.notifications.page = 1;

                loadNotifications(state.notifications.page, state.notifications.filter);
            });

            // Load more notifications
            $(document).on('click', '#pagination-list .page-link', function(e) {
                e.preventDefault();
                const $li = $(this).closest('.page-item');
                if ($li.hasClass('disabled') || $li.hasClass('active')) return;

                state.notifications.page = Number($(this).data('page')) || 1;

                loadNotifications(state.notifications.page, state.notifications.filter);
            });

            // Mark all as read
            $('#btn-mark-all-read').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: @js(route('api.user.notifications.read-all')),
                    method: 'PUT',
                    success: function(res) {
                        $('.notification-item').find('.notification-status-dot').removeClass('is-unread');
                        $('.notification-item').removeClass('unread').css('background', '');

                        updateUnreadBadge(0);
                        showToast(res.message, 'success');
                    },
                    error: function(res) {
                        showToast(res.responseJSON.message, 'danger');
                    }
                });
            });

            // Toggle read
            $('#notification-list').on('click', '.btn-toggle-read', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const $item = $(this).closest('.notification-item');
                const id = $item.data('id');
                const isRead = String($item.data('read')) === '1';
                const endpoint = isRead ? @js(route('api.user.notifications.unread')) + '/' + id :
                    @js(route('api.user.notifications.read')) + '/' + id;

                $.ajax({
                    url: endpoint,
                    method: 'PUT',
                    success: function(res) {
                        if (!isRead) {
                            // Change to read
                            $item.removeClass('unread').data('read', '1');
                            $item.find('.notification-status-dot').removeClass('is-unread');
                            $item.find('.notification-time').removeClass('is-unread');
                            $item.find('.btn-toggle-read').contents().last().replaceWith(
                                `{{ __('message.notification_mark_as_unread') }}`);
                            let count = parseInt($('#unread-badge').text()) || 0;

                            updateUnreadBadge(count - 1);
                        } else {
                            // Change to unread
                            $item.addClass('unread').data('read', '0');
                            $item.find('.notification-status-dot').addClass('is-unread');
                            $item.find('.notification-time').addClass('is-unread');
                            $item.find('.btn-toggle-read').contents().last().replaceWith(
                                `{{ __('message.notification_mark_as_read') }}`);
                            let count = parseInt($('#unread-badge').text()) || 0;

                            updateUnreadBadge(count + 1);
                        }
                    },
                    error: function(res) {}
                });
            });

            // Click notification opens campaign detail modal.
            $('#notification-list').on('click', '.notification-item', function(e) {
                if ($(e.target).closest('.dropdown, .btn-toggle-read, .btn-delete-notif').length) {
                    return;
                }

                if (!campaignModal) {
                    return;
                }

                const $item = $(this);
                const title = $item.data('campaign-title') || emptyCampaignTitle;
                const body = $item.data('campaign-body') || emptyCampaignBody;

                $('#notificationCampaignModalLabel').text(title);
                $('#notificationCampaignModalBody').text(body);
                campaignModal.show();
            });

            // Delete notification
            $('#notification-list').on('click', '.btn-delete-notif', function(e) {
                e.stopPropagation();

                const id = $(this).closest('.notification-item').data('id');
                const $item = $(this).closest('.notification-item');

                $.ajax({
                    url: @js(route('api.user.notifications.destroy')) + '/' + id,
                    method: 'DELETE',
                    success: function(res) {
                        $item.fadeOut(300, function() {
                            $(this).remove();
                        });
                        showToast(res.message, 'secondary');
                    },
                    error: function(res) {
                        showToast(res.responseJSON.message, 'danger');
                    }
                });
            });

            // Load notifications
            function loadNotifications(page, filter) {
                if (state.notifications.loading) return;
                state.notifications.loading = true;

                const params = {
                    page: page,
                    per_page: state.notifications.perPage
                };

                if (filter === 'unread') params.unread = 1;

                $.get(@js(route('api.user.notifications.list')), params)
                    .done(function(res) {
                        $notificationList.empty();

                        if (res.data.length === 0) {
                            $notificationList.html(
                                '<div class="text-center py-5 text-muted">' +
                                '<i class="bi bi-bell-slash fs-2 d-block mb-2"></i>' +
                                `{{ __('message.notification_empty') }}` + '</div>'
                            );
                            $notificationPager.empty();
                            $notificationWrap.hide();
                            updateUnreadBadge(res?.meta?.unread_count || 0);
                            return;
                        }

                        res.data.forEach(item => $notificationList.append(renderItem(item)));
                        renderPagination(res.meta, '#pagination-list', '#pagination-wrap');
                        updateUnreadBadge(res?.meta?.unread_count || 0);
                    })
                    .fail(function(res) {
                        showToast(res.responseJSON.message, 'danger');
                    })
                    .always(function() {
                        state.notifications.loading = false;
                    });
            }

            // Render item
            function renderItem(item) {
                const isUnread = !item.read_at;
                const timeAgo = formatTimeAgo(item.created_at);
                const toggleLabel = isUnread ? @js(__('message.notification_mark_as_read')) :
                    @js(__('message.notification_mark_as_unread'));
                const toggleIcon = isUnread ? 'bi-check2-circle' : 'bi-circle';
                const campaignTitle = (item.campaign && item.campaign.title)
                    ? item.campaign.title
                    : (item.title || '');
                const campaignBody = (item.campaign && item.campaign.body)
                    ? item.campaign.body
                    : (item.message || '');

                return `
      <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${item.id}" data-read="${item.read_at ? '1' : '0'}"
          data-campaign-title="${escapeHtml(campaignTitle)}" data-campaign-body="${escapeHtml(campaignBody)}">
          <div class="notification-icon">
              <i class="bi bi-megaphone-fill"></i>
          </div>

          <div class="notification-content">
              <div class="notification-title">
                  ${item.title || (item.campaign ? item.campaign.title : __('message.notification'))}
              </div>
              <div class="notification-message">
                  ${item.message || (item.campaign ? item.campaign.body : '')}
              </div>
              <div class="notification-time ${isUnread ? 'is-unread' : ''}">
                  ${timeAgo}
              </div>
          </div>

          <div class="notification-actions">
              <div class="dropdown">
                  <button class="btn btn-light btn-sm rounded-circle notification-action-btn" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-three-dots"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow notification-dropdown-menu">
                      <li>
                          <a class="dropdown-item py-2 btn-toggle-read" href="#">
                              <i class="bi ${toggleIcon} me-2 text-primary"></i>${toggleLabel}
                          </a>
                      </li>
                      <li>
                          <a class="dropdown-item py-2 btn-delete-notif text-danger" href="#">
                              <i class="bi bi-trash me-2"></i>{{ __('message.delete') }}
                          </a>
                      </li>
                  </ul>
              </div>

              <span class="notification-status-dot ${isUnread ? 'is-unread' : ''}"></span>
          </div>
      </div>`;
            }

            function escapeHtml(str) {
                return String(str || '')
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
            }

            // Update unread badge
            function updateUnreadBadge(count) {
                FirstCount = count;
                if (count > 0) {
                    $('#unread-badge').text(count).removeClass('unread-badge-hidden');
                } else {
                    $('#unread-badge').addClass('unread-badge-hidden');
                    $('#unread-badge').text('');
                }
            }
        });
    </script>
@endpush
