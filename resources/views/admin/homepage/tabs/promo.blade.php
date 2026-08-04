@push('admin_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    .choices__inner { background-color: #f8f9fa !important; border-radius: 0.5rem !important; border-color: var(--card-border) !important; padding: 5px 10px !important; min-height: 46px; }
    .choices__list--multiple .choices__item { background-color: var(--admin-accent) !important; border: 1px solid var(--admin-accent) !important; border-radius: 4px; }
</style>
@endpush

<div class="row g-4">
    <!-- Spin the Wheel Settings Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold font-heading text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-gift text-success me-2"></i>Spin the Wheel Configuration
            </h5>
            
            <form action="{{ route('admin.homepage.promo.update') }}" method="POST">
                @csrf
                
                <!-- Toggle Switch -->
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input text-success fs-5 shadow-none" type="checkbox" role="switch" name="lucky_wheel_enabled" id="luckyWheelEnabled" value="1" {{ \App\Models\Setting::get('lucky_wheel_enabled') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark ms-2 pt-1" for="luckyWheelEnabled">Enable Spin the Wheel Popup</label>
                </div>
                
                <!-- Delay Input -->
                <div class="mb-4">
                    <label for="luckyWheelDelay" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Display Delay (in seconds)</label>
                    <input type="number" min="0" name="lucky_wheel_delay" id="luckyWheelDelay" class="form-control bg-light border p-2.5" value="{{ \App\Models\Setting::get('lucky_wheel_delay', '5') }}" required placeholder="e.g. 5">
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Time to wait before showing the popup to the user on home page.</div>
                </div>
                
                <!-- Select Coupons -->
                <div class="mb-4">
                    <label for="luckyWheelCoupons" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Select Coupons for Wheel</label>
                    @php
                        $selectedCoupons = \App\Models\Setting::get('lucky_wheel_coupons', []);
                    @endphp
                    <select name="lucky_wheel_coupons[]" id="luckyWheelCoupons" class="form-select" multiple required>
                        @foreach($coupons as $coupon)
                            <option value="{{ $coupon->code }}" {{ is_array($selectedCoupons) && in_array($coupon->code, $selectedCoupons) ? 'selected' : '' }}>{{ $coupon->code }}</option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Selected coupons will be rendered as segments on the spin wheel.</div>
                </div>

                <!-- Select Winner -->
                <div class="mb-4">
                    <label for="luckyWheelWinner" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Forced Winning Coupon</label>
                    <select name="lucky_wheel_winner" id="luckyWheelWinner" class="form-select bg-light border p-2.5" required>
                        <option value="">-- Select Winner --</option>
                        @foreach($coupons as $coupon)
                            <option value="{{ $coupon->code }}" {{ \App\Models\Setting::get('lucky_wheel_winner') === $coupon->code ? 'selected' : '' }}>{{ $coupon->code }}</option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">The wheel will always land on this coupon to ensure the coupon is valid and active.</div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold text-uppercase font-heading" style="font-size: 0.85rem;">
                    <i class="bi bi-save me-2"></i>Save Wheel Settings
                </button>
            </form>
        </div>
    </div>

    <!-- Instructions / Promo Banner Info -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
            <h5 class="fw-bold font-heading text-dark border-bottom pb-2 mb-3">Promotional Information</h5>
            <p class="text-muted mb-4" style="font-size: 0.9rem;">The home page displays category-specific promo cards (Desi Ghee, Pure Honey, etc.). Edit landing links via categories list.</p>
            
            <div class="alert alert-success border-0 rounded-4 p-4 text-center bg-success bg-opacity-10 d-flex flex-column align-items-center justify-content-center h-75">
                <i class="bi bi-gift-fill text-success display-4 mb-3"></i>
                <h6 class="fw-bold text-success font-heading">Spin the Wheel Marketing Popup</h6>
                <p class="m-0 text-muted" style="font-size: 0.82rem; line-height: 1.5;">This popup uses dynamic CSS segments, canvas animation, and custom sounds. Ideal for special holidays like Mother's Day, Diwali, or New Year sales.</p>
            </div>
        </div>
    </div>
</div>

@push('admin_scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const couponsSelect = document.getElementById('luckyWheelCoupons');
    if (couponsSelect) {
        const choices = new Choices(couponsSelect, {
            removeItemButton: true,
            searchEnabled: true,
            placeholderValue: 'Select coupons for wheel...',
            itemSelectText: ''
        });

        // Update winner options dynamically when coupons are chosen
        const winnerSelect = document.getElementById('luckyWheelWinner');
        
        function updateWinnerOptions() {
            const selected = choices.getValue(true);
            const currentWinner = winnerSelect.value;
            winnerSelect.innerHTML = '<option value="">-- Select Winner --</option>';
            
            selected.forEach(code => {
                const opt = document.createElement('option');
                opt.value = code;
                opt.textContent = code;
                if (code === currentWinner) opt.selected = true;
                winnerSelect.appendChild(opt);
            });
        }
        
        couponsSelect.addEventListener('change', updateWinnerOptions);
    }
});
</script>
@endpush
