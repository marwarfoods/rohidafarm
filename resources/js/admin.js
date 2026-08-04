/**
 * RohidaFarm Admin Panel — Master JS Entry Point
 *
 * All modules are auto-initialized on DOMContentLoaded.
 * Charts are auto-detected via [data-chart] attributes — no window globals needed.
 */

import { initSidebar } from './admin/sidebar';
import { initTopbar  } from './admin/topbar';
import { initTables, showToast } from './admin/tables';
import { initForms   } from './admin/forms';
import { initCharts  } from './admin/charts';
import './media-gallery';
import './icon-picker';

// ── Bootstrap on DOM Ready ─────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  initTopbar();
  initTables();
  initForms();
  initCharts();   // scans [data-chart] elements automatically

  // ── Global toast exposed for Blade inline use ───────
  window.AdminToast = showToast;

  // ── Submit button loading state ─────────────────────
  document.addEventListener('click', (e) => {
    const btn = e.target.closest(
      'button[type="submit"]:not(.no-spinner), a.btn-admin-primary[href]:not([href="#"])'
    );
    if (!btn || btn.classList.contains('btn-loading')) return;
    btn.classList.add('btn-loading');
    btn.style.opacity = '0.75';
    btn.style.pointerEvents = 'none';
    setTimeout(() => {
      btn.classList.remove('btn-loading');
      btn.style.opacity = '';
      btn.style.pointerEvents = '';
    }, 5000);
  });

  // ── Flash session messages → toast ──────────────────
  document.querySelectorAll('[data-flash-toast]').forEach(el => {
    const type = el.getAttribute('data-flash-toast') || 'info';
    const msg  = el.textContent.trim();
    if (msg) showToast(type, msg);
    el.remove();
  });
});
