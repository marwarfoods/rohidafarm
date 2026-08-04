/**
 * Admin Sidebar — Collapse, Mobile Toggle
 */
export function initSidebar() {
  const sidebar     = document.getElementById('adminSidebar');
  const mainArea    = document.getElementById('adminMain');
  const toggleBtn   = document.getElementById('sidebarToggleBtn');
  const hamburger   = document.getElementById('topbarHamburger');
  const overlay     = document.getElementById('sidebarOverlay');

  if (!sidebar) return;

  const STORAGE_KEY = 'admin_sidebar_collapsed';

  // Restore saved state on desktop
  function restoreState() {
    if (window.innerWidth >= 992) {
      const collapsed = localStorage.getItem(STORAGE_KEY) === '1';
      setSidebarCollapsed(collapsed, false);
    }
  }

  function setSidebarCollapsed(collapsed, save = true) {
    if (collapsed) {
      sidebar.classList.add('sidebar-collapsed');
      mainArea && mainArea.classList.add('sidebar-collapsed');
      if (toggleBtn) {
        toggleBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        toggleBtn.title = 'Expand Sidebar';
      }
    } else {
      sidebar.classList.remove('sidebar-collapsed');
      mainArea && mainArea.classList.remove('sidebar-collapsed');
      if (toggleBtn) {
        toggleBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        toggleBtn.title = 'Collapse Sidebar';
      }
    }
    if (save) {
      localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
    }
  }

  function isCollapsed() {
    return sidebar.classList.contains('sidebar-collapsed');
  }

  // Desktop toggle
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      setSidebarCollapsed(!isCollapsed());
    });
  }

  // Mobile hamburger
  if (hamburger) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.add('sidebar-open');
      overlay && overlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    });
  }

  // Overlay close
  if (overlay) {
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('sidebar-open');
      overlay.classList.remove('active');
      document.body.style.overflow = '';
    });
  }

  // Close on Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar.classList.contains('sidebar-open')) {
      sidebar.classList.remove('sidebar-open');
      overlay && overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  });

  // Restore on page load
  restoreState();

  // Re-check on resize
  window.addEventListener('resize', () => {
    if (window.innerWidth < 992) {
      sidebar.classList.remove('sidebar-open');
      overlay && overlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  });
}
