/**
 * Admin Forms — Flatpickr date pickers, character counters,
 *               image previews, form validation helpers
 */
import flatpickr from 'flatpickr';

export function initForms() {
  // ── Flatpickr: date inputs ──────────────────────────
  document.querySelectorAll('[data-datepicker]').forEach(input => {
    const opts = {
      dateFormat: 'Y-m-d',
      allowInput: true,
      theme: 'light',
    };

    // Range picker
    if (input.dataset.datepicker === 'range') {
      opts.mode = 'range';
    }

    // Min/Max from data attrs
    if (input.dataset.minDate) opts.minDate = input.dataset.minDate;
    if (input.dataset.maxDate) opts.maxDate = input.dataset.maxDate;

    flatpickr(input, opts);
  });

  // ── Flatpickr: datetime inputs ──────────────────────
  document.querySelectorAll('[data-datetimepicker]').forEach(input => {
    flatpickr(input, {
      enableTime: true,
      dateFormat: 'Y-m-d H:i',
      time_24hr: true,
      allowInput: true,
    });
  });

  // ── Character counter ──────────────────────────────
  document.querySelectorAll('[data-char-count]').forEach(input => {
    const max    = parseInt(input.getAttribute('maxlength') || '255');
    const target = document.getElementById(input.dataset.charCount);
    if (!target) return;

    const update = () => {
      const remaining = max - input.value.length;
      target.textContent = `${input.value.length}/${max}`;
      target.style.color = remaining < 20 ? '#dc2626' : '#9ca3af';
    };

    input.addEventListener('input', update);
    update();
  });

  // ── Image preview ──────────────────────────────────
  document.querySelectorAll('[data-img-preview]').forEach(input => {
    const previewId = input.getAttribute('data-img-preview');
    const preview   = document.getElementById(previewId);
    if (!preview) return;

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file) return;

      const reader = new FileReader();
      reader.onload = e => {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });
  });

  // ── Confirm before leave on dirty form ─────────────
  document.querySelectorAll('form[data-confirm-leave]').forEach(form => {
    let dirty = false;

    form.querySelectorAll('input, textarea, select').forEach(el => {
      el.addEventListener('change', () => { dirty = true; });
      el.addEventListener('input',  () => { dirty = true; });
    });

    form.addEventListener('submit', () => { dirty = false; });

    window.addEventListener('beforeunload', (e) => {
      if (dirty) {
        e.preventDefault();
        e.returnValue = '';
      }
    });
  });

  // ── Slug generator ─────────────────────────────────
  const slugSource = document.getElementById('slugSource');
  const slugTarget = document.getElementById('slugTarget');
  if (slugSource && slugTarget) {
    slugSource.addEventListener('input', () => {
      if (slugTarget.dataset.manual === 'true') return;
      slugTarget.value = slugSource.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
    });

    slugTarget.addEventListener('input', () => {
      slugTarget.dataset.manual = 'true';
    });
  }
}
