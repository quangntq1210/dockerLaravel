@extends('layouts.user')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-7">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0">
        Thông báo
        <span id="unread-badge" class="badge bg-danger ms-1" style="display:none;"></span>
      </h4>
      <div class="dropdown">
        <button class="btn btn-light btn-sm" data-bs-toggle="dropdown">
          <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#" id="btn-mark-all-read">
              <i class="bi bi-check2-all me-2"></i>Đánh dấu tất cả là đã đọc
            </a>
          </li>
        </ul>
      </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-pills mb-3" id="notification-tabs">
      <li class="nav-item">
        <a class="nav-link active" href="#" data-filter="all" style="padding-left: 12px !important; padding-right: 12px !important; padding-top: 6px !important; padding-bottom: 6px !important; border-radius: 18px !important;">Tất cả</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-filter="unread" style="padding-left: 12px !important; padding-right: 12px !important; padding-top: 6px !important; padding-bottom: 6px !important; border-radius: 18px !important;">Chưa đọc</a>
      </li>
    </ul>

    {{-- Toast notification --}}
    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999"></div>

    {{-- Notification list --}}
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div id="notification-list">
          <div class="text-center py-5 text-muted" id="loading-spinner">
            <div class="spinner-border spinner-border-sm me-2"></div>
            Đang tải...
          </div>
        </div>
      </div>
    </div>

    {{-- Pagination --}}
    <nav aria-label="Phân trang" class="d-flex justify-content-center mt-2" id="pagination-wrap">
      <ul class="pagination" id="pagination-list">
      </ul>
    </nav>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(function() {
    let currentPage = 1;
    let currentFilter = 'all';
    let isLoading = false;

    // Load notifications
    loadNotifications(1, 'all', false);

    // Change tab
    $('#notification-tabs .nav-link').on('click', function(e) {
      e.preventDefault();
      $('#notification-tabs .nav-link').removeClass('active');
      $(this).addClass('active');
      currentFilter = $(this).data('filter');
      currentPage = 1;
      loadNotifications(1, currentFilter, false);
    });

    // Load more notifications
    $(document).on('click', '#pagination-list .page-link', function(e) {
      e.preventDefault();
      const $li = $(this).closest('.page-item');
      if ($li.hasClass('disabled') || $li.hasClass('active')) return;

      currentPage = parseInt($(this).data('page'));
      loadNotifications(currentPage, currentFilter, false);
    });

    // Mark all as read
    $('#btn-mark-all-read').on('click', function(e) {
      e.preventDefault();
      $.ajax({
        url: '/api/user/notifications/read-all',
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          $('.notification-item').find('.bg-primary .rounded-circle').remove();
          $('.notification-item').removeClass('unread').css('background', '');
          $('.notification-item').find('.dot-read').removeClass('bg-primary rounded-circle');
          updateUnreadBadge(0);
          showToast('Đã đánh dấu tất cả là đã đọc', 'success');
        },
        error: function() {
          showToast('Có lỗi xảy ra, vui lòng thử lại', 'danger');
        }
      });
    });

    // Mark single as read
    $('#notification-list').on('click', '.notification-item', function() {
      const id = $(this).data('id');
      const $item = $(this);

      if (!$item.hasClass('unread')) return;

      $.ajax({
        url: '/api/user/notifications/' + id + '/read',
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          $item.removeClass('unread').css('background', '');
          // Giảm badge
          let count = parseInt($('#unread-badge').text()) || 0;
          updateUnreadBadge(count - 1);
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
      const endpoint = isRead ?
        '/api/user/notifications/' + id + '/unread' :
        '/api/user/notifications/' + id + '/read';

      $.ajax({
        url: endpoint,
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          if (!isRead) {
            // Change to read
            $item.removeClass('unread').css('background', '').data('read', '1');
            $item.find('.dot-read').removeClass('bg-primary rounded-circle');
            $item.find('.btn-toggle-read').contents().last().replaceWith('Đánh dấu là chưa đọc');
            let count = parseInt($('#unread-badge').text()) || 0;
            updateUnreadBadge(count - 1);
          } else {
            // Change to unread
            $item.addClass('unread').css('background', '#e7f3ff').data('read', '0');
            $item.find('.dot-read').addClass('bg-primary rounded-circle');
            $item.find('.btn-toggle-read').contents().last().replaceWith('Đánh dấu là đã đọc');
            let count = parseInt($('#unread-badge').text()) || 0;
            updateUnreadBadge(count + 1);
          }
        },
        error: function() {
          showToast('Có lỗi xảy ra, vui lòng thử lại', 'danger');
        }
      });
    });

    // Delete notification
    $('#notification-list').on('click', '.btn-delete-notif', function(e) {
      e.stopPropagation();
      const id = $(this).closest('.notification-item').data('id');
      const $item = $(this).closest('.notification-item');

      $.ajax({
        url: '/api/user/notifications/' + id,
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          $item.fadeOut(300, function() {
            $(this).remove();
          });
          showToast('Đã xóa thông báo', 'secondary');
        }
      });
    });

    // Load notifications
    function loadNotifications(page, filter, append) {
      if (isLoading) return;
      isLoading = true;

      const params = {
        page: page,
        per_page: 8
      };
      if (filter === 'unread') params.unread = 1;

      $.get('/api/user/notifications', params)
        .done(function(res) {
          if (!append) {
            $('#notification-list').empty();
          }

          if (res.data.data.length === 0 && !append) {
            $('#notification-list').html(
              '<div class="text-center py-5 text-muted">' +
              '<i class="bi bi-bell-slash fs-2 d-block mb-2"></i>Không có thông báo nào</div>'
            );
            return;
          }

          $.each(res.data.data, function(i, item) {
            $('#notification-list').append(renderItem(item));
          });

          renderPagination(res.data.meta, '#pagination-list', '#pagination-wrap');

          updateUnreadBadge(res.data.meta.unread_count);
        })
        .fail(function() {
          showToast('Không thể tải thông báo', 'danger');
        })
        .always(function() {
          $('#loading-spinner').hide();
          isLoading = false;
        });
    }

    // Render item
    function renderItem(item) {
      const isUnread = !item.read_at;
      const timeAgo = formatTimeAgo(item.created_at);
      const bgStyle = isUnread ? 'background:#e7f3ff;' : '';
      const toggleLabel = isUnread ? 'Đánh dấu là đã đọc' : 'Đánh dấu là chưa đọc';
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
                  ${item.title || (item.campaign ? item.campaign.title : 'Thông báo')}
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
                              <i class="bi bi-trash me-2"></i>Xóa thông báo này
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

    // Update unread badge
    function updateUnreadBadge(count) {
      if (count > 0) {
        $('#unread-badge').text(count).show();
      } else {
        $('#unread-badge').hide();
      }
    }
  });
</script>
@endsection