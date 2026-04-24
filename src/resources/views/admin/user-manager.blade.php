@extends('admin.layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 data-lang="message.user_list"class="text-primary fw-bold mb-0">
                <i class="fas fa-users me-2"></i>{{ __('message.user_list') }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="userTable">
                   <thead class="table-light">
                   <tr>
                      <th data-lang="message.id">{{ __('message.id') }}</th>
                      <th data-lang="message.username">{{ __('message.username') }}</th>
                      <th data-lang="message.email">{{ __('message.email') }}</th>
                      <th data-lang="message.password_hash">{{ __('message.password_hash') }}</th>
                      <th data-lang="message.role">{{ __('message.role') }}</th>
                      <th data-lang="message.status">{{ __('message.status') }}</th>
                   </tr>
                   </thead>
                    <tbody id="userTableBody">
                        </tbody>
                </table>
                <div class="card-footer d-flex justify-content-between align-items-center">
    <div id="paginationInfo" class="small text-muted">
        </div>
    <nav id="paginationLinks">
        </nav>
</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>

    .table thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        color: #555;
        letter-spacing: 0.5px;
    }
    .password-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #eee;
    }
    .password-text {
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.9rem;
        color: #666;
        word-break: break-all;
    }
    .badge-role {
        font-size: 0.75rem;
        padding: 5px 10px;
    }
    .delete-btn {
    border: none;
    background: transparent;
    color: #dc3545;
    font-size: 16px;
    transition: all 0.2s ease;
    padding: 6px;
    border-radius: 6px;
}

.delete-btn:hover {
    background: #dc3545;
    color: #fff;
    transform: scale(1.1);
}

.delete-btn:active {
    transform: scale(0.95);
}
</style>
@endpush

@push('scripts')
<script>
    window.UserConfig = {
        messages: {
            loading: "{{ __('message.loading') }}",
            noData: "{{ __('message.no_data') }}",
            serverError: "{{ __('message.server_error') }}",
            verified: "{{ __('message.verified') }}",
            notVerified: "{{ __('message.not_verified') }}",
            viewHash: "{{ __('message.view_hash') }}"
        }
    };

    let currentPage = 1;

    function getDynamicMsg(key, defaultVal) {
        if (window.CRM_Admin && CRM_Admin.currentLangData) {
            return key.split('.').reduce((obj, i) => (obj ? obj[i] : null), CRM_Admin.currentLangData) || defaultVal;
        }
        return defaultVal;
    }

    async function fetchUsers(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('userTableBody');

        const msgLoading = getDynamicMsg('message.loading', UserConfig.messages.loading);

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">${msgLoading}</p>
                </td>
            </tr>`;

        try {
            const response = await fetch(`{{ route('admin.users.data') }}?page=${page}`);
            const result = await response.json();

            if (result.status === "success") {
                renderUserTable(result.data.data);
                renderPagination(result.data);
            }
        } catch (error) {
            const msgError = getDynamicMsg('message.server_error', UserConfig.messages.serverError);
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-5">${msgError}</td></tr>`;
        }
    }

    function renderUserTable(users) {
    const tbody = document.getElementById('userTableBody');
    tbody.innerHTML = '';

    if (!users || users.length === 0) {
        const msgNoData = getDynamicMsg('message.no_data', UserConfig.messages.noData);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4" data-lang="message.no_data">${msgNoData}</td></tr>`;
        return;
    }

    const msgVerified = getDynamicMsg('message.verified', UserConfig.messages.verified);
    const msgNotVerified = getDynamicMsg('message.not_verified', UserConfig.messages.notVerified);
    const msgViewHash = getDynamicMsg('message.view_hash', UserConfig.messages.viewHash);

    users.forEach((user, index) => {
        const tr = document.createElement('tr');
        const roleBadge = user.role === 'admin' ? 'bg-danger' : 'bg-primary';

        const verifiedAt = user.email_verified_at 
            ? `<span class="text-success" data-lang="message.verified"><i class="fas fa-check-circle me-1"></i>${msgVerified}</span>`
            : `<span class="text-muted small"><em data-lang="message.not_verified">${msgNotVerified}</em></span>`;

        tr.innerHTML = `
            <td>${user.id}</td>
            <td class="fw-bold">${user.name}</td>
            <td>${user.email}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="password-text me-2" id="pass-${index}" style="font-family: monospace;">••••••••</span>
                    <button class="btn btn-sm btn-outline-secondary border-0" 
                            onclick="togglePass('${user.password}', ${index})" 
                            data-lang="message.view_hash"
                            title="${msgViewHash}">
                        <i class="fas fa-eye" id="icon-${index}"></i>
                    </button>
                </div>
            </td>
            <td><span class="badge ${roleBadge} text-uppercase">${user.role}</span></td>
            <td>${verifiedAt}</td>
            <td>
   <button class="btn btn-sm btn-outline-danger delete-btn"
        onclick="deleteUser(${user.id}, ${user.role === 'admin'})">
    <i class="fas fa-trash"></i>
</button>
</td>
        `;
        tbody.appendChild(tr);
    });
}
    function renderPagination(pagination) {
        const container = document.getElementById('paginationLinks');
        if (!container) return;
        container.innerHTML = '';

        if (pagination.last_page <= 1) return;
        let html = `<ul class="pagination pagination-sm mb-0">`;

        html += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="fetchUsers(${pagination.current_page - 1})">&laquo;</a>
            </li>`;

        for (let i = 1; i <= pagination.last_page; i++) {
            html += `
                <li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="fetchUsers(${i})">${i}</a>
                </li>`;
        }

        html += `
            <li class="page-item ${pagination.current_page === pagination.last_page ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="fetchUsers(${pagination.current_page + 1})">&raquo;</a>
            </li>`;

        html += `</ul>`;
        container.innerHTML = html;
    }

    function togglePass(hash, index) {
        const textSpan = document.getElementById(`pass-${index}`);
        const icon = document.getElementById(`icon-${index}`);
        if (textSpan.textContent === "••••••••") {
            textSpan.textContent = hash;
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            textSpan.textContent = "••••••••";
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    document.addEventListener('DOMContentLoaded', () => fetchUsers(1));

    async function deleteUser(id, isAdmin = false) {
    if (isAdmin) {
        alert("Không thể xoá tài khoản admin!");
        return;
    }

    if (!confirm("Bạn có chắc muốn xoá user này?")) {
        return;
    }

    try {
        const response = await fetch(`{{ route('admin.users.delete') }}`, {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();

        if (result.status === "success") {
            alert(result.message);

            // reload lại bảng
            fetchUsers(currentPage);
        } else {
            alert(result.message || "Xoá thất bại");
        }

    } catch (error) {
        alert("Lỗi server, vui lòng thử lại");
    }
}
</script>
@endpush