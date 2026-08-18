<!-- ── Certifications & Trust Marks (Kasutam Pill Design + Certificate Modal) ── -->
<section class="py-4 py-md-5 certifications-kasutam-section" id="certifications">
    <div class="container">

        {{-- Section Header --}}
        <div class="text-center mb-4" data-aos="fade-up">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill font-heading fw-bold text-uppercase mb-3"
                 style="font-size: 0.78rem; letter-spacing: 2px; color: #5C3D2E; background: rgba(196, 154, 69, 0.15); border: 1px solid rgba(196, 154, 69, 0.35);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" stroke="#C49A45" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                100% Quality Guarantee
            </div>
            <h2 class="font-heading fw-bold cert-heading-title mb-2">Certifications & Trust Marks</h2>
            <p class="text-muted mx-auto" style="max-width: 560px; font-size: 0.92rem;">
                Our products strictly adhere to government quality standards, safety protocols, and organic food certifications.
            </p>
        </div>

        {{-- Certification Pills Row --}}
        <div class="cert-pills-row" data-aos="fade-up" data-aos-delay="100">

            @if(isset($certifications) && $certifications->isNotEmpty())
                @foreach($certifications as $cert)
                    <button type="button"
                            class="cert-pill-item"
                            data-cert-id="{{ $cert->id }}"
                            data-cert-name="{{ $cert->name }}"
                            data-cert-number="{{ $cert->certificate_number }}"
                            data-cert-images="{{ json_encode($cert->certificate_images ?? []) }}"
                            onclick="openCertModal(this)">
                        <div class="cert-pill-logo">
                            @if($cert->logo_path)
                                <img src="{{ asset($cert->logo_path) }}" alt="{{ $cert->name }} logo" loading="lazy">
                            @else
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#C49A45" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            @endif
                        </div>
                        <span class="cert-pill-text">{{ $cert->name }}</span>
                    </button>
                @endforeach

            @else
                {{-- Static fallback pills if no DB entries yet --}}
                @foreach([
                    ['name' => 'ISO 9001:2015', 'icon' => 'bi-award-fill'],
                    ['name' => 'FSSAI', 'icon' => 'bi-shield-check'],
                    ['name' => 'AGMARK', 'icon' => 'bi-patch-check-fill'],
                    ['name' => 'NABL Lab Tested', 'icon' => 'bi-file-earmark-medical-fill'],
                ] as $pill)
                    <div class="cert-pill-item">
                        <div class="cert-pill-logo">
                            <i class="bi {{ $pill['icon'] }}" style="color: #C49A45; font-size: 1.6rem;"></i>
                        </div>
                        <span class="cert-pill-text">{{ $pill['name'] }}</span>
                    </div>
                @endforeach
            @endif

        </div>

    </div>
</section>

{{-- ── Certificate Document Modal ── --}}
<div class="modal fade" id="certDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered cert-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0" id="certModalTitle">Certificate</h5>
                    <span class="counter-badge" id="certModalCounter"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="cert-doc-card">
                    <img id="certModalImg" src="" alt="Certificate" class="img-fluid">
                </div>
                {{-- Prev / Next for multi-page certs --}}
                <div class="d-flex align-items-center justify-content-center gap-3 mt-3" id="certNavRow">
                    <button class="nav-arrow-btn" onclick="certNavPrev()" id="certPrevBtn">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span class="fw-bold font-heading" style="color: #5C3D2E; font-size: 0.9rem;" id="certPageInfo">1 / 1</span>
                    <button class="nav-arrow-btn" onclick="certNavNext()" id="certNextBtn">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="certOpenFullBtn" target="_blank"
                   class="btn rounded-pill px-4 font-heading fw-bold"
                   style="background: #5C3D2E; color: #fff; font-size: 0.88rem; border: 2px solid #5C3D2E;">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Open Full Certificate
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    let _certImages = [];
    let _certPage = 0;

    function openCertModal(btn) {
        const images = JSON.parse(btn.dataset.certImages || '[]');
        if (!images.length) return;

        _certImages = images;
        _certPage = 0;

        document.getElementById('certModalTitle').textContent = btn.dataset.certName;
        document.getElementById('certModalCounter').textContent = btn.dataset.certNumber ? '🪪 ' + btn.dataset.certNumber : '';

        renderCertPage();
        new bootstrap.Modal(document.getElementById('certDocModal')).show();
    }

    function renderCertPage() {
        const img = document.getElementById('certModalImg');
        const pageInfo = document.getElementById('certPageInfo');
        const navRow = document.getElementById('certNavRow');
        const openBtn = document.getElementById('certOpenFullBtn');
        const imgPath = _certImages[_certPage];
        // Ensure we handle absolute URLs correctly, otherwise prepend slash for relative paths
        const url = (imgPath.startsWith('http://') || imgPath.startsWith('https://') || imgPath.startsWith('/')) 
            ? imgPath 
            : '/' + imgPath;

        img.src = url;
        img.alt = 'Certificate page ' + (_certPage + 1);
        pageInfo.textContent = (_certPage + 1) + ' / ' + _certImages.length;
        openBtn.href = url;
        navRow.style.display = _certImages.length > 1 ? 'flex' : 'none';

        document.getElementById('certPrevBtn').disabled = _certPage === 0;
        document.getElementById('certNextBtn').disabled = _certPage === _certImages.length - 1;
    }

    function certNavPrev() { if (_certPage > 0) { _certPage--; renderCertPage(); } }
    function certNavNext() { if (_certPage < _certImages.length - 1) { _certPage++; renderCertPage(); } }
</script>
