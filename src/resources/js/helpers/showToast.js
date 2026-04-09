const showToast = (message, type = 'success') => {
    const id = 'toast-' + Date.now();
    const html = `
      <div id="${id}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
          <div class="d-flex">
              <div class="toast-body">${message}</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
      </div>`;
    $('#toast-container').append(html);
    const toast = new bootstrap.Toast(document.getElementById(id), { delay: 2000 });
    toast.show();
}

export default showToast;

window.showToast = showToast;