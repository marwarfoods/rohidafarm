@if((request()->is('/') || request()->routeIs('home') || request()->path() === '/') && \App\Models\Setting::get('lucky_wheel_enabled'))
    @php
        $enabled = \App\Models\Setting::get('lucky_wheel_enabled');
        $delay = \App\Models\Setting::get('lucky_wheel_delay', '5');
        $wheelCoupons = \App\Models\Setting::get('lucky_wheel_coupons', []);
        $winnerCoupon = \App\Models\Setting::get('lucky_wheel_winner');
    @endphp

    <div id="luckyWheelPopup" class="lucky-wheel-overlay" style="display: none;" data-delay="{{ $delay }}">
        <div class="lucky-wheel-container">
            <button type="button" class="lucky-wheel-close" id="luckyWheelClose">&times;</button>
            
            <div class="lucky-wheel-header text-center">
                <span class="lucky-wheel-subtitle text-uppercase">Try Your Luck</span>
                <h4 class="lucky-wheel-title font-heading fw-bold">Spin & Win Big!</h4>
                <p class="lucky-wheel-desc">Enter your email and spin the wheel to win exclusive discounts.</p>
            </div>

            <!-- The Wheel Area -->
            <div class="lucky-wheel-wrapper">
                <div class="lucky-wheel-arrow"></div>
                <canvas id="luckyWheelCanvas" width="320" height="320" data-segments='@json($wheelCoupons)' data-winner="{{ $winnerCoupon }}"></canvas>
                <button type="button" id="luckyWheelSpinBtn">SPIN</button>
            </div>

            <!-- Input area -->
            <div class="lucky-wheel-form mt-4">
                <div class="input-group">
                    <input type="email" id="luckyWheelEmail" class="form-control" placeholder="Enter your email address" required>
                    <button type="button" id="luckyWheelSubmitBtn" class="btn btn-success">Spin Now</button>
                </div>
                <div id="luckyWheelError" class="text-danger small mt-1 text-center" style="display: none;">Please enter a valid email address.</div>
            </div>
        </div>
    </div>

    <!-- Win Modal Popup Overlay -->
    <div id="luckyWheelWinModal" class="lucky-wheel-win-overlay" style="display: none;">
        <div class="lucky-wheel-win-box text-center">
            <div class="confetti-container"></div>
            <i class="bi bi-trophy-fill text-warning display-4 mb-3 d-block animate-bounce"></i>
            <h4 class="font-heading fw-bold text-success">Congratulations!</h4>
            <p class="text-muted">You won an exclusive coupon code:</p>
            <div class="lucky-wheel-code-box mb-4">
                <span id="luckyWheelWinCode">XXXXXX</span>
                <button type="button" id="luckyWheelCopyBtn" class="btn btn-sm btn-outline-success ms-2">
                    <i class="bi bi-copy"></i> Copy
                </button>
            </div>
            <button type="button" id="luckyWheelWinClose" class="btn btn-success px-4 rounded-pill">Start Shopping</button>
        </div>
    </div>
@endif
