/**
 * Admin Tables — Confirm delete modal, row highlight, search filter
 */
export function initTables() {
  // ── Confirm Delete Modal ────────────────────────────
  // Usage: <button data-delete-url="/..." data-delete-name="Item Name" data-bs-target="#adminDeleteModal">
  const deleteModal = document.getElementById('adminDeleteModal');
  const deleteForm  = document.getElementById('adminDeleteForm');
  const deleteName  = document.getElementById('adminDeleteName');

  if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', (e) => {
      const btn  = e.relatedTarget;
      const url  = btn?.getAttribute('data-delete-url');
      const name = btn?.getAttribute('data-delete-name') || 'this item';

      if (deleteForm && url)   deleteForm.action = url;
      if (deleteName && name) deleteName.textContent = name;
    });
  }

  // ── Row flash on page load (after redirect with anchor) ─
  const hash = window.location.hash;
  if (hash && hash.startsWith('#row-')) {
    const row = document.querySelector(hash);
    if (row) {
      row.style.background = 'rgba(36,132,67,0.08)';
      row.style.transition = 'background 1s ease';
      setTimeout(() => row.style.background = '', 2000);
      row.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  // ── Live search filter for admin tables ─────────────
  const liveSearch = document.getElementById('adminTableSearch');
  if (liveSearch) {
    const tbody = document.querySelector(liveSearch.getAttribute('data-table') || '.admin-table tbody');
    if (tbody) {
      liveSearch.addEventListener('input', () => {
        const q = liveSearch.value.toLowerCase().trim();
        tbody.querySelectorAll('tr').forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = (!q || text.includes(q)) ? '' : 'none';
        });
      });
    }
  }
}

/**
 * Toast notification system (lightweight, no Toastr dependency)
 * @param {'success'|'error'|'warning'|'info'} type
 * @param {string} message
 */
export function showToast(type, message) {
  const icons = {
    success: 'bi-check-circle-fill',
    error:   'bi-x-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    info:    'bi-info-circle-fill',
  };

  let container = document.getElementById('admin-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'admin-toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `admin-toast toast-${type}`;
  toast.innerHTML = `
    <div class="toast-icon"><i class="bi ${icons[type] || icons.info}"></i></div>
    <div class="toast-message">${message}</div>
    <button class="toast-close" aria-label="Close"><i class="bi bi-x"></i></button>
  `;

  container.appendChild(toast);

  toast.querySelector('.toast-close').addEventListener('click', () => removeToast(toast));
  setTimeout(() => removeToast(toast), 4500);
}

function removeToast(toast) {
  toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
  toast.style.opacity    = '0';
  toast.style.transform  = 'translateX(16px)';
  setTimeout(() => toast.remove(), 300);
}
