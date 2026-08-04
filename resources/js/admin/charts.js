/**
 * Admin Charts — Chart.js + ApexCharts
 *
 * Auto-initializes from data-* attributes on container elements.
 * No window globals needed — eliminates race conditions.
 *
 * Usage in Blade:
 *   <canvas data-chart="doughnut"
 *           data-labels='@json($labels)'
 *           data-values='@json($values)'></canvas>
 *
 *   <div data-chart="area"
 *        data-labels='@json($labels)'
 *        data-values='@json($values)'></div>
 *
 *   <div data-chart="bar"
 *        data-labels='@json($labels)'
 *        data-values='@json($values)'></div>
 */

import Chart from 'chart.js/auto';

// ── Brand palette ───────────────────────────────────────
const palette = {
  green:   '#248443',
  blue:    '#2563eb',
  amber:   '#d97706',
  red:     '#dc2626',
  teal:    '#0891b2',
  purple:  '#7c3aed',
  gray:    '#94a3b8',
};

// ── Chart.js global defaults ───────────────────────────
Chart.defaults.font.family  = "'DM Sans', sans-serif";
Chart.defaults.font.size    = 12;
Chart.defaults.color        = '#6b7280';

// ── Shared tooltip style ───────────────────────────────
const tooltipDefaults = {
  backgroundColor: '#ffffff',
  titleColor:      '#1c1f24',
  bodyColor:       '#6b7280',
  borderColor:     '#ECE7DD',
  borderWidth:     1,
  padding:         10,
  cornerRadius:    8,
};

// ══════════════════════════════════════════════════════
// Chart.js renderers
// ══════════════════════════════════════════════════════

function renderLineChart(canvas, labels, values) {
  new Chart(canvas, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Revenue (₹)',
        data: values,
        borderColor: palette.green,
        backgroundColor: 'rgba(36,132,67,0.07)',
        borderWidth: 2.5,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: palette.green,
        pointRadius: 4,
        pointHoverRadius: 7,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          ...tooltipDefaults,
          callbacks: {
            label: ctx => ` ₹${Number(ctx.raw).toLocaleString('en-IN')}`,
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(0,0,0,0.04)' },
          ticks: { callback: v => '₹' + Number(v).toLocaleString('en-IN', { notation: 'compact' }) }
        },
        x: { grid: { display: false } }
      }
    }
  });
}

function renderDoughnutChart(canvas, labels, values) {
  new Chart(canvas, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: [palette.amber, palette.blue, palette.green, palette.red, palette.gray, palette.teal, palette.purple],
        borderWidth: 3,
        borderColor: '#ffffff',
        hoverBorderWidth: 3,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '68%',
      plugins: {
        legend: { position: 'bottom', labels: { padding: 16, boxWidth: 10 } },
        tooltip: { ...tooltipDefaults }
      }
    }
  });
}

// ══════════════════════════════════════════════════════
// ApexCharts renderers (lazy-loaded to avoid 504 on fail)
// ══════════════════════════════════════════════════════

async function renderAreaChart(el, labels, values) {
  try {
    const { default: ApexCharts } = await import('apexcharts');
    new ApexCharts(el, {
      series: [{ name: 'Revenue ₹', data: values }],
      chart: {
        type: 'area', height: el.dataset.height || 300,
        toolbar: { show: false },
        fontFamily: "'DM Sans', sans-serif",
        zoom: { enabled: false },
        animations: { enabled: true, speed: 700 },
      },
      colors: [palette.green],
      fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] }
      },
      dataLabels: { enabled: false },
      stroke: { curve: 'smooth', width: 2.5 },
      xaxis: {
        categories: labels,
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
      },
      yaxis: {
        labels: {
          style: { fontSize: '11px', colors: '#9ca3af' },
          formatter: v => '₹' + Number(v).toLocaleString('en-IN', { notation: 'compact' }),
        }
      },
      grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
      tooltip: { theme: 'light', y: { formatter: v => '₹' + Number(v).toLocaleString('en-IN') } },
      markers: { size: 0, hover: { size: 5 } }
    }).render();
  } catch (err) {
    // Fallback to Chart.js line if ApexCharts fails
    console.warn('[AdminCharts] ApexCharts failed, falling back to Chart.js:', err.message);
    el.innerHTML = '<canvas></canvas>';
    renderLineChart(el.querySelector('canvas'), labels, values);
  }
}

async function renderBarChart(el, labels, values) {
  try {
    const { default: ApexCharts } = await import('apexcharts');
    new ApexCharts(el, {
      series: [{ name: 'Orders', data: values }],
      chart: {
        type: 'bar', height: el.dataset.height || 240,
        toolbar: { show: false },
        fontFamily: "'DM Sans', sans-serif",
        animations: { enabled: true, speed: 600 },
      },
      colors: [palette.green],
      plotOptions: { bar: { borderRadius: 6, columnWidth: '52%' } },
      dataLabels: { enabled: false },
      xaxis: {
        categories: labels,
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { fontSize: '11px', colors: '#9ca3af' } },
      },
      yaxis: {
        labels: {
          style: { fontSize: '11px', colors: '#9ca3af' },
          formatter: v => Math.round(v),
        }
      },
      grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
      tooltip: { theme: 'light', y: { formatter: v => `${v} orders` } },
    }).render();
  } catch (err) {
    console.warn('[AdminCharts] ApexCharts bar failed:', err.message);
  }
}

// ══════════════════════════════════════════════════════
// Auto-init: scan DOM for [data-chart] elements
// ══════════════════════════════════════════════════════

export function initCharts() {
  document.querySelectorAll('[data-chart]').forEach(el => {
    const type   = el.dataset.chart;
    const labels = JSON.parse(el.dataset.labels || '[]');
    const values = JSON.parse(el.dataset.values || '[]');

    switch (type) {
      case 'line':     renderLineChart(el, labels, values);    break;
      case 'doughnut': renderDoughnutChart(el, labels, values); break;
      case 'area':     renderAreaChart(el, labels, values);     break;
      case 'bar':      renderBarChart(el, labels, values);      break;
    }
  });
}
