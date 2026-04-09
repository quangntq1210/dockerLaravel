function renderPagination(meta, listSelector, wrapSelector) {
    const lastPage = Math.ceil(meta.total / meta.per_page);
    const current = meta.current_page;

    if (lastPage <= 1) {
        $(wrapSelector).hide();
        return;
    }

    let html = `<li class="page-item ${current === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current - 1}">Previous</a>
  </li>`;

    for (let i = 1; i <= lastPage; i++) {
        html += `<li class="page-item ${i === current ? 'active' : ''}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
    }

    html += `<li class="page-item ${current === lastPage ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${current + 1}">Next</a>
  </li>`;

    $(listSelector).html(html);
    $(wrapSelector).show();
}
