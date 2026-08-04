// =================================================
// Direct Buy Module - Razorpay Checkout
// resources/js/product-detail/direct-buy.js
// =================================================

export function initDirectBuy() {
    const form = document.getElementById('directBuyForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn = document.getElementById('btnDirectBuySubmit');
        const spinner = document.getElementById('directBuySpinner');
        const errorContainer = document.getElementById('directBuyError');
        
        // Reset state
        errorContainer.classList.add('d-none');
        btn.disabled = true;
        spinner.classList.remove('d-none');

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const res = await fetch('/checkout/direct-buy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();

            if (!res.ok) {
                throw new Error(result.message || 'Something went wrong while initializing payment.');
            }

            // Initialize Razorpay
            var options = {
                "key": result.key,
                "amount": result.amount,
                "currency": "INR",
                "name": result.app_name || "RohidaFarm",
                "description": "Direct Order Payment",
                "order_id": result.razorpayOrderId,
                "handler": function (response) {
                    // Show verifying payment overlay
                    const overlay = document.createElement('div');
                    overlay.style.position = 'fixed';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.width = '100vw';
                    overlay.style.height = '100vh';
                    overlay.style.backgroundColor = 'rgba(255, 255, 255, 0.9)';
                    overlay.style.zIndex = '9999';
                    overlay.style.display = 'flex';
                    overlay.style.flexDirection = 'column';
                    overlay.style.alignItems = 'center';
                    overlay.style.justifyContent = 'center';
                    overlay.innerHTML = `
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
                        <h4 class="font-heading fw-bold text-dark">Verifying Payment...</h4>
                        <p class="text-muted">Please do not close this window or press back.</p>
                    `;
                    document.body.appendChild(overlay);

                    // Create hidden form to submit callback
                    const callbackForm = document.createElement('form');
                    callbackForm.method = 'POST';
                    callbackForm.action = '/checkout/razorpay-callback';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token');
                    
                    const rpId = document.createElement('input');
                    rpId.type = 'hidden'; rpId.name = 'razorpay_payment_id'; rpId.value = response.razorpay_payment_id;
                    
                    const roId = document.createElement('input');
                    roId.type = 'hidden'; roId.name = 'razorpay_order_id'; roId.value = response.razorpay_order_id;
                    
                    const rs = document.createElement('input');
                    rs.type = 'hidden'; rs.name = 'razorpay_signature'; rs.value = response.razorpay_signature;
                    
                    const uuid = document.createElement('input');
                    uuid.type = 'hidden'; uuid.name = 'uuid'; uuid.value = result.uuid;

                    callbackForm.appendChild(csrf);
                    callbackForm.appendChild(rpId);
                    callbackForm.appendChild(roId);
                    callbackForm.appendChild(rs);
                    callbackForm.appendChild(uuid);

                    document.body.appendChild(callbackForm);
                    callbackForm.submit();
                },
                "prefill": {
                    "name": result.user.name,
                    "email": result.user.email,
                    "contact": result.user.phone
                },
                "theme": {
                    "color": "#02042b"
                },
                "modal": {
                    "ondismiss": function() {
                        // Create hidden form to cancel
                        const cancelForm = document.createElement('form');
                        cancelForm.method = 'POST';
                        cancelForm.action = '/checkout/razorpay-cancel/' + result.uuid;
                        
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token');
                        cancelForm.appendChild(csrf);

                        document.body.appendChild(cancelForm);
                        cancelForm.submit();
                    }
                }
            };

            var rzp = new Razorpay(options);
            
            rzp.on('payment.failed', function (response){
                alert("Payment Failed: " + response.error.description);
                // The ondismiss handler will cancel the order when they close the modal.
            });

            // Hide bootstrap modal before showing Razorpay
            const bsModal = bootstrap.Modal.getInstance(document.getElementById('directBuyModal'));
            if(bsModal) bsModal.hide();

            rzp.open();

        } catch (error) {
            errorContainer.textContent = error.message;
            errorContainer.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            spinner.classList.add('d-none');
        }
    });
}
