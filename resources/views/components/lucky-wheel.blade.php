@if((request()->is('/') || request()->routeIs('home') || request()->path() === '/') && \App\Models\Setting::get('lucky_wheel_enabled'))
    @php
        $enabled = \App\Models\Setting::get('lucky_wheel_enabled');
        $delay = \App\Models\Setting::get('lucky_wheel_delay', '5');
        $wheelCoupons = \App\Models\Setting::get('lucky_wheel_coupons', []);
        $winnerCoupon = \App\Models\Setting::get('lucky_wheel_winner');
    @endphp

    <div id="luckyWheelPopup" class="lucky-wheel-overlay" style="display: none;" data-delay="{{ $delay }}">
        <div class="lucky-wheel-container" style="background-image: url('{{ asset('images/vectors/pop-up-bg.png') }}');">
            <button type="button" class="lucky-wheel-close" id="luckyWheelClose"><i class="bi bi-x-lg"></i></button>

            <!-- Left Column: Wheel -->
            <div class="lucky-wheel-col lucky-wheel-col-left text-center">
                <div class="lucky-wheel-header">
                    <span class="lucky-wheel-subtitle text-uppercase">🌿 Try Your Luck 🌿</span>
                    <h4 class="lucky-wheel-title font-heading fw-bold">Spin & Win Big!</h4>
                    <p class="lucky-wheel-desc">Enter your details below and spin the wheel to win exclusive discounts.</p>
                </div>

                <div class="lucky-wheel-wrapper">
                    <div class="lucky-wheel-arrow"></div>
                    <canvas id="luckyWheelCanvas" width="320" height="320" data-segments='@json($wheelCoupons)' data-winner="{{ $winnerCoupon }}"></canvas>
                    <button type="button" id="luckyWheelSpinBtn">SPIN</button>
                </div>
            </div>

            <div class="lucky-wheel-divider d-none d-md-block"></div>

            <!-- Right Column: Form -->
            <div class="lucky-wheel-col lucky-wheel-col-right text-center">
                <div class="lucky-wheel-header">
                    <h4 class="lucky-wheel-title font-heading fw-bold">Enter Your Details</h4>
                    <p class="lucky-wheel-desc">Fill in your details to spin the wheel and win amazing prizes!</p>
                </div>

                <div class="lucky-wheel-form mt-4 text-start">
                    <label for="luckyWheelName" class="lucky-wheel-label">Your Name</label>
                    <div class="lucky-wheel-input-group mb-3">
                        <i class="bi bi-person"></i>
                        <input type="text" id="luckyWheelName" class="form-control" placeholder="Enter your name" required>
                    </div>

                    <label for="luckyWheelMobile" class="lucky-wheel-label">Mobile Number</label>
                    <div class="lucky-wheel-input-group mb-4">
                        <i class="bi bi-telephone"></i>
                        <input type="tel" id="luckyWheelMobile" class="form-control" placeholder="Enter your mobile number" required maxlength="10" inputmode="numeric" pattern="[0-9]*">
                    </div>

                    <button type="button" id="luckyWheelSubmitBtn" class="btn btn-success w-100">Spin Now & Win <i class="bi bi-gift-fill ms-1"></i></button>
                    <div id="luckyWheelError" class="text-danger small mt-2 text-center" style="display: none;">Please enter your name and a valid 10-digit mobile number.</div>
                </div>
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
            <a href="{{ route('shop.index') }}" id="luckyWheelWinClose" class="btn btn-success px-4 rounded-pill">Start Shopping</a>
        </div>
    </div>
@endif
