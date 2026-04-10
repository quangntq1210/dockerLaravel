const TYPE_TO_COLOR = {
    success: 'success',
    danger: 'danger',
    error: 'danger',
    warning: 'warning',
    info: 'info',
    secondary: 'secondary',
    primary: 'primary',
    light: 'light',
    dark: 'dark',
};

const LIGHT_BACKGROUNDS = new Set(['warning', 'light']);

function resolveColor(type) {
    if (type == null || type === '') {
        return 'primary';
    }
    const key = String(type).toLowerCase();
    return TYPE_TO_COLOR[key] || key;
}

function escapeHtml(text) {
    if (text == null) {
        return '';
    }
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function getToastContainer() {
    let el = document.getElementById('toast-container');
    if (!el) {
        el = document.createElement('div');
        el.id = 'toast-container';
        el.className = 'toast-container position-fixed top-0 end-0 p-3';
        el.style.zIndex = '1090';
        document.body.appendChild(el);
    }
    return el;
}

function showToast(message, type = 'success', options = {}) {
    const { delay = 3500, autohide = true } = options;
    const color = resolveColor(type);
    const closeWhite = !LIGHT_BACKGROUNDS.has(color);

    const id = `toast-${Date.now()}-${Math.random().toString(36).slice(2, 11)}`;
    const container = getToastContainer();

    const toastEl = document.createElement('div');
    toastEl.id = id;
    toastEl.className = `toast align-items-center text-bg-${color} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    const closeClass = closeWhite ? 'btn-close btn-close-white' : 'btn-close';

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${escapeHtml(message)}</div>
            <button type="button" class="${closeClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toastEl);

    const toast = new bootstrap.Toast(toastEl, {
        autohide,
        delay,
    });

    toastEl.addEventListener('hidden.bs.toast', () => {
        toast.dispose();
        toastEl.remove();
    });

    toast.show();
}

export default showToast;

window.showToast = showToast;