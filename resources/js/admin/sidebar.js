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

  // ── Menu search / filter (instant, no Enter) ──────────
  const search = document.getElementById('sidebarSearch');
  if (search) {
    const groups = Array.from(sidebar.querySelectorAll('.sidebar-nav'));
    const titles = Array.from(sidebar.querySelectorAll('.sidebar-section-title'));

    groups.forEach(g => { g.style.display = 'flex'; g.style.flexDirection = 'column'; });

    const applyFilter = () => {
      const q = search.value.trim().toLowerCase();

      groups.forEach(group => {
        Array.from(group.children).forEach(li => {
          const label = (li.querySelector('.sidebar-label')?.textContent || '').toLowerCase();
          if (!q) {
            li.hidden = false;
            li.style.order = '';
            return;
          }
          const match = label.includes(q);
          li.hidden = !match;
          li.style.order = label.startsWith(q) ? '-1' : '0';
        });
      });

      titles.forEach(title => {
        const group = title.nextElementSibling;
        const hasVisible = group && group.classList.contains('sidebar-nav')
          && Array.from(group.children).some(li => !li.hidden);
        title.hidden = Boolean(q) && !hasVisible;
      });
    };

    search.addEventListener('input', applyFilter);
    search.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { search.value = ''; applyFilter(); search.blur(); }
    });
  }

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
