@extends('user.layouts.user')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold mb-0">
                    {{ __('message.notification') }}
                    <span id="unread-badge" class="badge bg-danger ms-1" style="display:none;"></span>
                </h4>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
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

            {{-- Tabs --}}
            <ul class="nav nav-pills mb-3" id="notification-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-filter="all"
                        style="padding-left: 12px !important; padding-right: 12px !important; padding-top: 6px !important; padding-bottom: 6px !important; border-radius: 18px !important;">{{ __('message.all') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-filter="unread"
                        style="padding-left: 12px !important; padding-right: 12px !important; padding-top: 6px !important; padding-bottom: 6px !important; border-radius: 18px !important;">{{ __('message.unread') }}</a>
                </li>
            </ul>

            {{-- Toast notification --}}
            <div id="toast-container" class="position-fixed top-0 end-0 p-2 d-flex flex-column gap-2"
                style="z-index: 9999; margin-top: 55px;"></div>

            {{-- Notification list --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div id="notification-list">
                        <div class="text-center py-5 text-muted" id="loading-spinner">
                            <div class="spinner-border spinner-border-sm me-2"></div>
                            {{ __('message.loading') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            <nav aria-label="{{ __('message.pagination') }}" class="d-flex justify-content-center mt-2"
                id="pagination-wrap">
                <ul class="pagination" id="pagination-list">
                </ul>
            </nav>

            {{-- Table Campaign --}}
            <h4 class="fw-bold mb-0">
                {{ __('message.campaigns') }}
            </h4>

            <div id="campaign-table">
                <table class="table table-bordered table-hover mt-3">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" id="select-all-campaigns"></th>
                            <th style="font-size: 14px">{{ __('message.title') }}</th>
                            <th style="font-size: 14px">{{ __('message.body') }}</th>
                        </tr>
                    </thead>
                    <tbody id="campaign-table-body">
                    </tbody>
                </table>
            </div>


            <div class="d-flex justify-content-between align-items-center mt-2">
                {{-- Pagination Campaign --}}
                <nav aria-label="{{ __('message.pagination') }}" class="d-flex justify-content-center mt-2"
                    id="pagination-campaign-wrap">
                    <ul class="pagination" id="pagination-campaign-list">
                    </ul>
                </nav>

                {{-- Button Send Campaign --}}
                <div class="d-flex justify-content-end mt-2">
                    <button class="btn btn-primary" id="btn-send-campaign">
                        {{ __('message.subcribe_campaign') }}
                    </button>
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
                },
                campaigns: {
                    page: 1,
                    lastPage: 1,
                    loading: false,
                    perPage: 7
                }
            };

            const $notificationList = $('#notification-list');
            const $notificationPager = $('#pagination-list');
            const $notificationWrap = $('#pagination-wrap');

            const $campaignBody = $('#campaign-table-body');
            const $campaignPager = $('#pagination-campaign-list');
            const $campaignWrap = $('#pagination-campaign-wrap');

            // Load notifications & campaigns
            loadNotifications(1, state.notifications.filter);
            loadCampaigns(1);

            // Select all campaigns
            $('#select-all-campaigns').on('change', function(e) {
                e.preventDefault();
                const isChecked = $(this).prop('checked');
                $campaignBody.find('input[type="checkbox"]').prop('checked', isChecked);
            });

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

            // Load more campaigns
            $(document).on('click', '#pagination-campaign-list .page-link', function(e) {
                e.preventDefault();
                const $li = $(this).closest('.page-item');
                if ($li.hasClass('disabled') || $li.hasClass('active')) return;

                state.campaigns.page = Number($(this).data('page')) || 1;

                loadCampaigns(state.campaigns.page);
            });

            // Mark all as read
            $('#btn-mark-all-read').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: @js(route('api.user.notifications.read-all')),
                    method: 'PUT',
                    success: function(res) {
                        $('.notification-item').find('.bg-primary .rounded-circle').remove();
                        $('.notification-item').removeClass('unread').css('background', '');
                        $('.notification-item').find('.dot-read').removeClass(
                            'bg-primary rounded-circle');

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
                            $item.removeClass('unread').css('background', '').data('read', '1');
                            $item.find('.dot-read').removeClass('bg-primary rounded-circle');
                            $item.find('.btn-toggle-read').contents().last().replaceWith(
                                `{{ __('message.notification_mark_as_unread') }}`);
                            let count = parseInt($('#unread-badge').text()) || 0;

                            updateUnreadBadge(count - 1);
                        } else {
                            // Change to unread
                            $item.addClass('unread').css('background', '#e7f3ff').data('read',
                                '0');
                            $item.find('.dot-read').addClass('bg-primary rounded-circle');
                            $item.find('.btn-toggle-read').contents().last().replaceWith(
                                `{{ __('message.notification_mark_as_read') }}`);
                            let count = parseInt($('#unread-badge').text()) || 0;

                            updateUnreadBadge(count + 1);
                        }
                        // showToast(res.message, 'success');
                    },
                    error: function(res) {
                        // showToast(res.responseJSON.message, 'danger');
                    }
                });
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

            // Send campaign
            $('#btn-send-campaign').on('click', function(e) {
                e.preventDefault();
                const campaigns = $campaignBody.find('input[type="checkbox"]:checked').map(function() {
                    return $(this).data('id');
                }).toArray();

                if (campaigns.length === 0) {
                    showToast(@js(__('message.please_select_at_least_one_campaign')), 'danger');
                    return;
                }

                $.ajax({
                    url: @js(route('campaigns.recipients.store.bulk')),
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        campaigns: campaigns
                    }),
                    success: function(res) {
                        showToast(res.message, 'success');

                        const currentPageCount = $('#campaign-table-body .campaign-checkbox')
                            .length;

                        if (currentPageCount == campaigns.length && state.campaigns.page ==
                            state.campaigns.lastPage) {
                            loadCampaigns(state.campaigns.page - 1);
                        } else {
                            loadCampaigns(state.campaigns.page);
                        }

                        $('#select-all-campaigns').prop('checked', false);
                    },
                    error: function(res) {
                        showToast(res?.responseJSON?.errors?.campaigns[0] ||
                            @js(__('message.error_occurred')), 'danger');
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

            // Load campaigns
            function loadCampaigns(page) {
                if (state.campaigns.loading) return;
                state.campaigns.loading = true;

                const params = {
                    page: page,
                    per_page: state.campaigns.perPage
                };

                $.get(@js(route('campaigns.draft')), params)
                    .done(function(res) {
                        $campaignBody.empty();

                        if (!res?.data?.length) {
                            $campaignBody.html(
                                `<tr><td colspan="3" class="text-center text-muted py-4">{{ __('message.campaigns_empty') }}</td></tr>`
                            );
                            $campaignPager.empty();
                            $campaignWrap.hide();
                            return;
                        }

                        state.campaigns.lastPage = res?.meta?.last_page || 1;

                        res.data.forEach(item => $campaignBody.append(renderCampaignTable(item)));
                        renderPagination(res.meta, '#pagination-campaign-list', '#pagination-campaign-wrap');
                    })
                    .fail(function(res) {
                        showToast(res?.responseJSON?.message || @js(__('message.error_occurred')), 'danger');
                    })
                    .always(function() {
                        state.campaigns.loading = false;
                    });
            }

            // Render item
            function renderItem(item) {
                const isUnread = !item.read_at;
                const timeAgo = formatTimeAgo(item.created_at);
                const bgStyle = isUnread ? 'background:#e7f3ff;' : '';
                const toggleLabel = isUnread ? @js(__('message.notification_mark_as_read')) :
                    @js(__('message.notification_mark_as_unread'));

                const toggleIcon = isUnread ? 'bi-check2-circle' : 'bi-circle';

                return `
      <div class="notification-item position-relative d-flex align-items-start gap-3 px-3 py-3 border-bottom ${isUnread ? 'unread' : ''}"
          data-id="${item.id}" data-read="${item.read_at ? '1' : '0'}"
          style="cursor:pointer; transition: background 0.2s; ${bgStyle}">

          {{-- Avatar icon --}}
          <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
              style="width:48px;height:48px;font-size:18px">
              <i class="bi bi-megaphone-fill"></i>
          </div>

          {{-- Content --}}
          <div class="flex-grow-1">
              <div class="fw-semibold" style="font-size:14px">
                  ${item.title || (item.campaign ? item.campaign.title : __('message.notification'))}
              </div>
              <div class="text-muted" style="font-size:13px;line-height:1.4">
                  ${item.message || (item.campaign ? item.campaign.body : '')}
              </div>
              <div class="${isUnread ? 'text-primary fw-semibold' : 'text-muted'} mt-1" style="font-size:12px">
                  ${timeAgo}
              </div>
          </div>

          {{-- Merge position-absolute into 1 flex div --}}
          <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-center">
              {{-- Three dots button --}}
              <div class="dropdown">
                  <button class="btn btn-light btn-sm rounded-circle p-1"
                          style="width:32px;height:32px;line-height:1;"
                          data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-three-dots"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:220px;font-size:14px">
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

              {{-- Green dot unread --}}
              ${isUnread
                  ? '<span class="dot-read bg-primary rounded-circle" style="width:10px;height:10px;display:inline-block"></span>'
                  : '<span class="dot-read" style="width:10px;height:10px;display:inline-block"></span>'
              }


          </div>
      </div>`;
            }

            // Render campaign table
            function renderCampaignTable(data) {
                return `
                    <tr id="campaign-table-row-${data.id}">
                        <td style="font-size:13px;line-height:1.4;"><input type="checkbox" class="form-check-input campaign-checkbox" data-id="${data.id}"></td>
                        <td style="font-size:13px;line-height:1.4;">${data.title}</td>
                        <td style="font-size:13px;line-height:1.4;">${data.body}</td>
                    </tr>
                `
            }

            // Update unread badge
            function updateUnreadBadge(count) {
                FirstCount = count;
                if (count > 0) {
                    $('#unread-badge').text(count).show();
                } else {
                    $('#unread-badge').hide();
                    $('#unread-badge').text('');
                }
            }
        });
    </script>
@endpush
