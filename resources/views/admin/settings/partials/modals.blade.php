<!-- Delhivery Help Modal -->
<div class="modal fade" id="delhiveryHelpModal" tabindex="-1" aria-labelledby="delhiveryHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="delhiveryHelpModalLabel">
                    <i class="bi bi-box-seam text-primary me-2"></i>How to connect Delhivery?
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ol class="text-muted" style="font-size: 0.9rem; line-height: 1.8;">
                    <li>Log in to your <strong>Delhivery One / Partner Portal</strong> account.</li>
                    <li>Go to <strong>Settings &gt; API Center</strong> (or contact your Delhivery account manager to enable API access).</li>
                    <li>Generate/copy your <strong>API Token</strong> (a long alphanumeric key).</li>
                    <li>Note the <strong>Pickup Location Name</strong> you registered with Delhivery for your warehouse.</li>
                    <li>Note your registered <strong>Client Name</strong> (used to generate waybills); leave blank to reuse the Pickup Location Name.</li>
                    <li>Paste the Token, Pickup Location Name and Client Name into the settings fields here.</li>
                    <li>Click <strong>Save Configurations</strong>. Future orders will automatically sync!</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Google Analytics Help Modal -->
<div class="modal fade" id="gaHelpModal" tabindex="-1" aria-labelledby="gaHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="gaHelpModalLabel">
                    <i class="bi bi-graph-up text-primary me-2"></i>How to get GA4 Measurement ID?
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ol class="text-muted" style="font-size: 0.9rem; line-height: 1.8;">
                    <li>Log in to your <strong>Google Analytics</strong> account.</li>
                    <li>Click on <strong>Admin</strong> (gear icon) at the bottom left.</li>
                    <li>Under the Property column, click on <strong>Data Streams</strong>.</li>
                    <li>Select your website's data stream. (Create one if none exists).</li>
                    <li>Look for the <strong>Measurement ID</strong> at the top right. It starts with <code>G-</code> (e.g., <code>G-1234567890</code>).</li>
                    <li>Copy that ID and paste it here in the settings.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Test Modal -->
<div class="modal fade" id="razorpayTestModal" tabindex="-1" aria-labelledby="razorpayTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="razorpayTestModalLabel">
                    <i class="bi bi-play-circle text-success me-2"></i>Test Razorpay
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Enter a test amount in INR to verify the Sandbox Key.</p>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">₹</span>
                    <input type="number" id="rzp_test_amount" class="form-control" placeholder="Amount" value="100">
                </div>
                <button type="button" id="btn_rzp_test_pay" class="btn btn-success w-100 rounded-pill fw-bold text-uppercase" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important;">
                    Pay Now
                </button>
            </div>
        </div>
    </div>
</div>
