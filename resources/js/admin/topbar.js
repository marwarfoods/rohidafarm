/**
 * Admin Topbar — Search, Notification Bell, User Dropdown
 */
export function initTopbar() {
  // ── User dropdown ──────────────────────────────────
  const userBtn      = document.getElementById('topbarUserBtn');
  const userDropdown = document.getElementById('topbarUserDropdown');

  if (userBtn && userDropdown) {
    userBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = userDropdown.classList.contains('d-block');
      userDropdown.classList.toggle('d-block', !isOpen);
      userDropdown.classList.toggle('d-none',  isOpen);
    });

    document.addEventListener('click', () => {
      userDropdown.classList.remove('d-block');
      userDropdown.classList.add('d-none');
    });
  }

  // ── Page title from data attribute ─────────────────
  const topbarTitle = document.getElementById('topbarPageTitle');
  const pageMeta    = document.querySelector('[data-page-title]');
  if (topbarTitle && pageMeta) {
    topbarTitle.textContent = pageMeta.getAttribute('data-page-title');
  }

  // ── Auto-dismiss flash alerts ──────────────────────
  document.querySelectorAll('.admin-flash-alert').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.4s ease';
      el.style.opacity    = '0';
      setTimeout(() => el.remove(), 400);
    }, 4000);
  });
}
