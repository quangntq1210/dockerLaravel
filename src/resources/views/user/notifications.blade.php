@extends('layouts.user')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-7">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0">
        Thông báo
        <span id="unread-badge" class="badge bg-danger ms-1" style="display:none"></span>
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
        <a class="nav-link active" href="#" data-filter="all">Tất cả</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" data-filter="unread">Chưa đọc</a>
      </li>
    </ul>

    {{-- Toast thông báo --}}
    <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999"></div>

    {{-- Danh sách thông báo --}}
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
    <div class="text-center mt-3" id="pagination-wrap" style="display:none!important"></div>
    <div class="text-center mt-3">
      <button class="btn btn-light btn-sm px-4" id="btn-load-more" style="display:none">
        Xem thêm thông báo
      </button>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
  $(function() {
    let currentPage = 1;
    let currentFilter = 'all';
    let isLoading = false;

    // Load lần đầu
    loadNotifications(1, 'all', false);

    // Đổi tab
    $('#notification-tabs .nav-link').on('click', function(e) {
      e.preventDefault();
      $('#notification-tabs .nav-link').removeClass('active');
      $(this).addClass('active');
      currentFilter = $(this).data('filter');
      currentPage = 1;
      loadNotifications(1, currentFilter, false);
    });

    // Load more
    $('#btn-load-more').on('click', function() {
      currentPage++;
      loadNotifications(currentPage, currentFilter, true);
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
          // Cập nhật UI: bỏ dot unread tất cả item
          $('.notification-dot').remove();
          $('.notification-item').removeClass('unread').css('background', '');
          updateUnreadBadge(0);
          showToast('Đã đánh dấu tất cả là đã đọc', 'success');
        },
        error: function() {
          showToast('Có lỗi xảy ra, vui lòng thử lại', 'danger');
        }
      });
    });

    // Mark single as read (delegate vì item được render động)
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
          $item.find('.notification-dot').remove();
          // Giảm badge
          let count = parseInt($('#unread-badge').text()) || 0;
          updateUnreadBadge(count - 1);
        }
      });
    });

    $('#notification-list').on('click', '.btn-toggle-read', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const $item = $(this).closest('.notification-item');
      const id = $item.data('id');
      const isRead = $item.data('read') == '1';

      $.ajax({
        url: '/api/user/notifications/' + id + '/read',
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          if (!isRead) {
            // Chuyển sang đã đọc
            $item.removeClass('unread').css('background', '').data('read', '1');
            $item.find('.notification-dot').remove();
            $item.find('.btn-toggle-read i').attr('class', 'bi bi-circle me-2 text-primary');
            $item.find('.btn-toggle-read').contents().last().replaceWith('Đánh dấu là chưa đọc');
            let count = parseInt($('#unread-badge').text()) || 0;
            updateUnreadBadge(count - 1);
          } else {
            // Chuyển sang chưa đọc (cần thêm endpoint mark-as-unread)
            $item.addClass('unread').css('background', '#e7f3ff').data('read', '0');
            $item.find('.btn-toggle-read i').attr('class', 'bi bi-check2-circle me-2 text-primary');
            $item.find('.btn-toggle-read').contents().last().replaceWith('Đánh dấu là đã đọc');
            let count = parseInt($('#unread-badge').text()) || 0;
            updateUnreadBadge(count + 1);
          }
        }
      });
    });

    // Xóa notification
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

    // ---- FUNCTIONS ----

    function loadNotifications(page, filter, append) {
      if (isLoading) return;
      isLoading = true;
      $('#btn-load-more').prop('disabled', true).text('Đang tải...');

      const params = {
        page: page,
        per_page: 10
      };
      if (filter === 'unread') params.unread = 1;

      $.get('/api/user/notifications', params)
        .done(function(res) {
          if (!append) {
            $('#notification-list').empty();
          }

          if (res.data.length === 0 && !append) {
            $('#notification-list').html(
              '<div class="text-center py-5 text-muted">' +
              '<i class="bi bi-bell-slash fs-2 d-block mb-2"></i>Không có thông báo nào</div>'
            );
            $('#btn-load-more').hide();
            return;
          }

          $.each(res.data, function(i, item) {
            $('#notification-list').append(renderItem(item));
          });

          // Hiện/ẩn Load more
          const hasMore = res.meta.current_page * res.meta.per_page < res.meta.total;
          $('#btn-load-more').toggle(hasMore).prop('disabled', false).text('Xem thêm thông báo');

          updateUnreadBadge(res.meta.unread_count);
        })
        .fail(function() {
          showToast('Không thể tải thông báo', 'danger');
        })
        .always(function() {
          $('#loading-spinner').hide();
          isLoading = false;
        });
    }

    function renderItem(item) {
      const isUnread = !item.read_at;
      const timeAgo = formatTimeAgo(item.created_at);
      const bgStyle = isUnread ? 'background:#e7f3ff;' : '';
      const dotHtml = isUnread ?
        '<span class="notification-dot bg-primary rounded-circle position-absolute" style="width:10px;height:10px;bottom:12px;right:12px"></span>' :
        '';
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

        {{-- Nội dung --}}
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

        {{-- Bỏ position-absolute riêng lẻ, gộp vào 1 div flex --}}
        <div class="d-flex align-items-center gap-2 flex-shrink-0 align-self-center">
            {{-- Nút 3 chấm --}}
            <div class="dropdown">
                <button class="btn btn-light btn-sm rounded-circle p-1"
                        style="width:32px;height:32px;line-height:1"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        onclick="event.stopPropagation()">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:220px;font-size:14px">
                    <li>
                        <a class="dropdown-item py-2 btn-toggle-read" href="#" onclick="event.stopPropagation()">
                            <i class="bi ${toggleIcon} me-2 text-primary"></i>${toggleLabel}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 btn-delete-notif text-danger" href="#" onclick="event.stopPropagation()">
                            <i class="bi bi-trash me-2"></i>Xóa thông báo này
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Chấm xanh unread --}}
            ${isUnread
                ? '<span class="bg-primary rounded-circle" style="width:10px;height:10px;display:inline-block"></span>'
                : '<span style="width:10px;height:10px;display:inline-block"></span>'
            }


        </div>
    </div>`;
    }

    function updateUnreadBadge(count) {
      if (count > 0) {
        $('#unread-badge').text(count).show();
      } else {
        $('#unread-badge').hide();
      }
    }

    function showToast(message, type) {
      const id = 'toast-' + Date.now();
      const html = `
        <div id="${id}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
      $('#toast-container').append(html);
      const toast = new bootstrap.Toast(document.getElementById(id), {
        delay: 3000
      });
      toast.show();
    }

    function formatTimeAgo(dateStr) {
      const now = new Date();
      const date = new Date(dateStr);
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / 60000);
      if (diffMins < 1) return 'Vừa xong';
      if (diffMins < 60) return diffMins + ' phút trước';
      const diffHours = Math.floor(diffMins / 60);
      if (diffHours < 24) return diffHours + ' giờ trước';
      const diffDays = Math.floor(diffHours / 24);
      if (diffDays < 7) return diffDays + ' ngày trước';
      return date.toLocaleDateString('vi-VN');
    }
  });
</script>
@endsection