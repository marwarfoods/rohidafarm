@extends('layouts.admin')

@push('admin_styles')
    <link rel="stylesheet" href="{{ asset('admin/css/settings.css') }}">
    <style>
        .settings-tab-link { color: #333 !important; font-weight: 500; cursor: pointer; transition: all 0.2s ease; }
        .settings-tab-link.active { color: #000 !important; background-color: #e8f5e9 !important; font-weight: 600; }
        .settings-tab-link i { color: #555 !important; }
        .settings-tab-link.active i { color: #000 !important; }
    </style>
@endpush

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-gear text-success me-2"></i>Dynamic Configurations</h1>
</div>

<div class="row g-4">
    <!-- Left Column: Navigation Sidebar -->
    <div class="col-lg-3">
        <div class="position-sticky bg-white p-3 rounded-4 shadow-sm border mb-4" style="top: 30px; border-color: var(--border-color) !important;">
            <h5 class="font-heading fw-bold text-dark border-bottom pb-3 mb-3 ps-2">Settings Menu</h5>
            <ul class="nav nav-pills flex-column gap-2" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link active" id="site-tab" data-bs-toggle="pill" href="#site" role="tab" aria-controls="site" aria-selected="true">
                        <i class="bi bi-display me-2"></i>Site Settings
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="smtp-tab" data-bs-toggle="pill" href="#smtp" role="tab" aria-controls="smtp" aria-selected="false">
                        <i class="bi bi-envelope-at me-2"></i>SMTP Credentials
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="seo-tab" data-bs-toggle="pill" href="#seo" role="tab" aria-controls="seo" aria-selected="false">
                        <i class="bi bi-search me-2"></i>SEO Parameters
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="payments-tab" data-bs-toggle="pill" href="#payments" role="tab" aria-controls="payments" aria-selected="false">
                        <i class="bi bi-credit-card-2-front me-2"></i>Payment Gateways
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="shipping-tab" data-bs-toggle="pill" href="#shipping" role="tab" aria-controls="shipping" aria-selected="false">
                        <i class="bi bi-truck me-2"></i>Shipping & Taxes
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="integrations-tab" data-bs-toggle="pill" href="#integrations" role="tab" aria-controls="integrations" aria-selected="false">
                        <i class="bi bi-boxes me-2"></i>Integrations
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="faqs-tab" data-bs-toggle="pill" href="#faqs" role="tab" aria-controls="faqs" aria-selected="false">
                        <i class="bi bi-question-circle me-2"></i>Global FAQs
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="auth-tab" data-bs-toggle="pill" href="#auth" role="tab" aria-controls="auth" aria-selected="false">
                        <i class="bi bi-shield-lock me-2"></i>Authentication & Google OAuth
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Middle Column: Modular Tabbed Forms -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <form action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                @csrf
                
                <!-- Hidden Input for Active Tab Persistence -->
                <input type="hidden" name="active_tab" id="activeTabInput" value="{{ old('active_tab', '#site') }}">
                
                <div class="tab-content" id="settingsTabContent">
                    @include('admin.settings.partials.site')
                    @include('admin.settings.partials.smtp')
                    @include('admin.settings.partials.seo')
                    @include('admin.settings.partials.payments')
                    @include('admin.settings.partials.shipping')
                    @include('admin.settings.partials.integrations')
                    @include('admin.settings.partials.faqs')
                    @include('admin.settings.partials.auth')
                </div>

                <div class="border-top pt-4 mt-5">
                    <button type="submit" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                        <i class="bi bi-save me-2"></i>Save Configurations
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Dynamic Diagnostic Panels -->
    <div class="col-lg-3">
        <div class="position-sticky" style="top: 30px;">
            @include('admin.settings.partials.sidebars')
        </div>
    </div>
</div>

<!-- Settings Helper Modals -->
@include('admin.settings.partials.modals')

@endsection

@push('admin_scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Password toggle functionality
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
                    const icon = this.querySelector('i');
                    if (input && input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else if (input) {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });

            // Tax Mode Container Toggle
            const taxModeSelect = document.getElementById('taxModeSelect');
            if (taxModeSelect) {
                taxModeSelect.addEventListener('change', function() {
                    if (this.value === 'all_india') {
                        document.getElementById('taxAllIndiaContainer').style.display = 'block';
                        document.getElementById('taxStateWiseContainer').style.display = 'none';
                    } else {
                        document.getElementById('taxAllIndiaContainer').style.display = 'none';
                        document.getElementById('taxStateWiseContainer').style.display = 'block';
                    }
                });
            }

            // Tab State URL Hash Persistence
            function activateTabFromHash() {
                let hash = window.location.hash;
                if (!hash) {
                    hash = document.getElementById('activeTabInput').value || '#site';
                }
                if (hash) {
                    const tabTrigger = document.querySelector(`.settings-tab-link[href="${hash}"]`);
                    if (tabTrigger) {
                        const tabInstance = bootstrap.Tab.getOrCreateInstance(tabTrigger);
                        tabInstance.show();
                        document.getElementById('activeTabInput').value = hash;

                        // Sync Sidebar Panel
                        const tabId = hash.replace('#', '');
                        document.querySelectorAll('.dynamic-sidebar-card').forEach(card => card.classList.remove('active'));
                        const sideCard = document.getElementById('sidebar-' + tabId);
                        if (sideCard) sideCard.classList.add('active');
                    }
                }
            }

            activateTabFromHash();

            // Listen for Tab Switching
            document.querySelectorAll('.settings-tab-link').forEach(link => {
                link.addEventListener('shown.bs.tab', function(e) {
                    const hash = e.target.getAttribute('href');
                    if (hash) {
                        history.replaceState(null, null, hash);
                        document.getElementById('activeTabInput').value = hash;

                        // Sync Sidebar Panel
                        const tabId = hash.replace('#', '');
                        document.querySelectorAll('.dynamic-sidebar-card').forEach(card => card.classList.remove('active'));
                        const sideCard = document.getElementById('sidebar-' + tabId);
                        if (sideCard) sideCard.classList.add('active');
                    }
                });
            });

            // Razorpay Sandbox Test Transaction
            const btnPay = document.getElementById('btn_rzp_test_pay');
            if(btnPay) {
                btnPay.addEventListener('click', function() {
                    const keyInput = document.getElementById('rzp_key_input').value;
                    const amountInput = document.getElementById('rzp_test_amount').value;
                    
                    if(!keyInput) {
                        alert('Please enter a Razorpay Sandbox Key ID first!');
                        return;
                    }
                    if(!amountInput || amountInput <= 0) {
                        alert('Please enter a valid amount.');
                        return;
                    }

                    var options = {
                        "key": keyInput,
                        "amount": amountInput * 100,
                        "currency": "INR",
                        "name": "RohidaFarm Sandbox",
                        "description": "Test Transaction",
                        "image": "{{ asset(\App\Models\Setting::get('favicon') ?: 'favicon.ico') }}",
                        "handler": function (response){
                            alert('Success! Payment ID: ' + response.razorpay_payment_id);
                            const modalEl = document.getElementById('razorpayTestModal');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if(modal) modal.hide();
                        },
                        "prefill": {
                            "name": "Admin Tester",
                            "email": "admin@rohidafarm.com",
                            "contact": "9999999999"
                        },
                        "theme": {
                            "color": "#2c6e49"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.on('payment.failed', function (response){
                        alert('Payment Failed: ' + response.error.description);
                    });
                    rzp1.open();
                });
            }

            // Google OAuth Connection Test
            const btnTestGoogle = document.getElementById('btnTestGoogleOAuth');
            if (btnTestGoogle) {
                btnTestGoogle.addEventListener('click', function () {
                    const clientId = document.getElementById('inputGoogleClientId').value.trim();
                    const clientSecret = document.getElementById('inputGoogleClientSecret').value.trim();
                    const resultDiv = document.getElementById('googleTestResult');

                    btnTestGoogle.disabled = true;
                    btnTestGoogle.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';
                    resultDiv.className = 'mt-3 alert alert-info';
                    resultDiv.classList.remove('d-none');
                    resultDiv.textContent = 'Validating configuration...';

                    fetch('{{ route("admin.settings.google.test") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            google_client_id: clientId,
                            google_client_secret: clientSecret
                        })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(res => {
                        btnTestGoogle.disabled = false;
                        btnTestGoogle.innerHTML = '<i class="bi bi-patch-check me-1"></i> Test Connection';
                        if (res.status === 200 && res.body.status === 'success') {
                            resultDiv.className = 'mt-3 alert alert-success fw-bold';
                            resultDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + res.body.message;
                        } else {
                            resultDiv.className = 'mt-3 alert alert-danger fw-bold';
                            resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (res.body.message || 'Validation failed.');
                        }
                    })
                    .catch(err => {
                        btnTestGoogle.disabled = false;
                        btnTestGoogle.innerHTML = '<i class="bi bi-patch-check me-1"></i> Test Connection';
                        resultDiv.className = 'mt-3 alert alert-danger fw-bold';
                        resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Connection test failed.';
                    });
                });
            }

            // Cloudflare Turnstile Connection Test
            const btnTestTurnstile = document.getElementById('btnTestTurnstile');
            if (btnTestTurnstile) {
                btnTestTurnstile.addEventListener('click', function () {
                    const siteKey = document.getElementById('inputTurnstileSiteKey').value.trim();
                    const secretKey = document.getElementById('inputTurnstileSecretKey').value.trim();
                    const resultDiv = document.getElementById('turnstileTestResult');

                    btnTestTurnstile.disabled = true;
                    btnTestTurnstile.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Verifying with Cloudflare...';
                    resultDiv.className = 'mt-3 alert alert-info';
                    resultDiv.classList.remove('d-none');
                    resultDiv.textContent = 'Contacting Cloudflare Turnstile API...';

                    fetch('{{ route("admin.settings.turnstile.test") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            turnstile_site_key: siteKey,
                            turnstile_secret_key: secretKey
                        })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(res => {
                        btnTestTurnstile.disabled = false;
                        btnTestTurnstile.innerHTML = '<i class="bi bi-shield-check me-1"></i> Test Connection';
                        if (res.status === 200 && res.body.status === 'success') {
                            resultDiv.className = 'mt-3 alert alert-success fw-bold';
                            resultDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + res.body.message;
                        } else {
                            resultDiv.className = 'mt-3 alert alert-danger fw-bold';
                            resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (res.body.message || 'Validation failed.');
                        }
                    })
                    .catch(err => {
                        btnTestTurnstile.disabled = false;
                        btnTestTurnstile.innerHTML = '<i class="bi bi-shield-check me-1"></i> Test Connection';
                        resultDiv.className = 'mt-3 alert alert-danger fw-bold';
                        resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Connection test failed.';
                    });
                });
            }

            // Global FAQs Repeater
            const btnAddGlobalFaq = document.getElementById('btnAddGlobalFaq');
            const globalFaqsContainer = document.getElementById('globalFaqsContainer');
            let globalFaqIndex = {{ count($globalFaqs ?? []) }};

            function refreshGlobalFaqIndexes() {
                if (!globalFaqsContainer) return;
                const cards = globalFaqsContainer.querySelectorAll('.global-faq-item-card');
                cards.forEach((card, i) => {
                    const q = card.querySelector('.global-faq-q');
                    const a = card.querySelector('.global-faq-a');
                    const s = card.querySelector('.global-faq-sort');
                    if (q) q.name = `global_faqs[${i}][question]`;
                    if (a) a.name = `global_faqs[${i}][answer]`;
                    if (s) { s.name = `global_faqs[${i}][sort_order]`; s.value = i; }
                });
            }

            function appendGlobalFaq(question = '', answer = '') {
                if (!globalFaqsContainer) return;
                document.getElementById('globalFaqsEmpty')?.remove();

                const card = document.createElement('div');
                card.className = 'global-faq-item-card card border rounded-3 p-3 bg-light shadow-sm';
                card.innerHTML = `
                    <div class="d-flex align-items-start gap-3">
                        <div class="global-faq-drag-handle text-muted pt-2" style="cursor: grab;" title="Drag to reorder">
                            <i class="bi bi-grip-vertical fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size: 0.8rem;">Question</label>
                                <input type="text" name="global_faqs[${globalFaqIndex}][question]" class="form-control form-control-sm border bg-white global-faq-q" required value="${question}" placeholder="e.g. How is Rohida Farm A2 Ghee made?">
                            </div>
                            <div>
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size: 0.8rem;">Answer</label>
                                <textarea name="global_faqs[${globalFaqIndex}][answer]" class="form-control form-control-sm border bg-white global-faq-a" rows="2" required placeholder="Detailed answer...">${answer}</textarea>
                            </div>
                            <input type="hidden" class="global-faq-sort" name="global_faqs[${globalFaqIndex}][sort_order]" value="${globalFaqIndex}">
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex align-items-center justify-content-center btn-remove-global-faq" style="width: 28px; height: 28px;" title="Delete FAQ">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                globalFaqsContainer.appendChild(card);
                globalFaqIndex++;
                card.querySelector('.btn-remove-global-faq').addEventListener('click', () => {
                    card.remove();
                    refreshGlobalFaqIndexes();
                });
            }

            btnAddGlobalFaq?.addEventListener('click', () => appendGlobalFaq());

            globalFaqsContainer?.querySelectorAll('.btn-remove-global-faq').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.global-faq-item-card').remove();
                    refreshGlobalFaqIndexes();
                });
            });

            if (globalFaqsContainer && typeof Sortable !== 'undefined') {
                Sortable.create(globalFaqsContainer, {
                    animation: 180,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: '.global-faq-drag-handle',
                    onEnd: refreshGlobalFaqIndexes
                });
            }
        });
    </script>
@endpush
