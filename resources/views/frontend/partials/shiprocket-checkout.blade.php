{{--
    Shiprocket Checkout (Fastrr) launcher — rendered only when the integration is active.
    Script/CSS URLs and HeadlessCheckout.addToCart(event, token, {fallbackUrl}) are from the
    official "Checkout UI Flow" docs. The access token is generated server-side; no key or
    secret is ever sent to the browser. Any failure falls back to the native checkout.
--}}
@php
    $srCheckout = app(\App\Services\ShiprocketCheckoutService::class);
    $srBuyNow = $srCheckout->isActiveFor('buy_now');
    $srCart = $srCheckout->isActiveFor('cart');
@endphp
@if ($srBuyNow || $srCart)
    <link rel="stylesheet" href="{{ $srCheckout->styleUrl() }}">
    <input type="hidden" id="sellerDomain" value="{{ parse_url(config('app.url'), PHP_URL_HOST) ?: request()->getHost() }}">
    <script src="{{ $srCheckout->scriptUrl() }}" defer></script>
    <script>
        (function () {
            const cfg = {
                tokenUrl: @json(route('shiprocket-checkout.token')),
                nativeCheckout: @json(route('checkout.index')),
                buyNow: @json($srBuyNow),
                cart: @json($srCart),
            };
            const selectors = [];
            if (cfg.buyNow) selectors.push('#btnBuyNowDirect', '#mobileBtnBuyNowDirect');
            if (cfg.cart) selectors.push('[data-shiprocket-checkout="cart"]');
            if (!selectors.length) return;

            let busy = false;

            function csrf() {
                const meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function buyNowPayload() {
                const productInput = document.querySelector('#mainAddToCartForm [name="product_id"], #mobileAddToCartForm [name="product_id"]');
                const activeVariant = document.querySelector('.variant-card.active');
                const variantId = activeVariant
                    ? activeVariant.getAttribute('data-id')
                    : (document.getElementById('hiddenVariantId')?.value || document.getElementById('mobileHiddenVariantId')?.value || '');
                const qty = parseInt(document.getElementById('purchaseQuantity')?.value || document.getElementById('mobilePurchaseQuantity')?.value || '1', 10) || 1;
                return {
                    source: 'buy_now',
                    product_id: productInput ? parseInt(productInput.value, 10) : null,
                    variant_id: variantId ? parseInt(variantId, 10) : null,
                    quantity: qty,
                };
            }

            // Capture phase so this runs before the native Buy Now / checkout handlers.
            async function onClick(e) {
                const el = e.target.closest(selectors.join(','));
                if (!el || el.disabled) return;

                const isCart = el.matches('[data-shiprocket-checkout="cart"]');
                const payload = isCart ? { source: 'cart' } : buyNowPayload();
                if (!isCart && !payload.product_id) return; // not a product page → native flow

                e.preventDefault();
                e.stopImmediatePropagation();
                if (busy) return;
                busy = true;

                const originalHtml = el.innerHTML;
                el.setAttribute('aria-busy', 'true');
                el.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Checkout...';

                const nativeFallback = isCart ? (el.getAttribute('href') || cfg.nativeCheckout) : null;
                let fallbackUrl = nativeFallback;

                try {
                    const res = await fetch(cfg.tokenUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json().catch(() => ({}));
                    fallbackUrl = data.fallback_url || fallbackUrl;

                    if (!res.ok || !data.ok || !data.token || typeof window.HeadlessCheckout === 'undefined') {
                        throw new Error(data.message || 'Shiprocket Checkout unavailable');
                    }

                    window.HeadlessCheckout.addToCart(e, data.token, { fallbackUrl: fallbackUrl || cfg.nativeCheckout });
                    el.innerHTML = originalHtml;
                    el.removeAttribute('aria-busy');
                    busy = false;
                } catch (err) {
                    console.warn('Shiprocket Checkout → native checkout:', err.message);
                    if (fallbackUrl) {
                        window.location.href = fallbackUrl;
                        return;
                    }
                    // Buy Now with no server fallback URL: hand back to the native handler.
                    el.innerHTML = originalHtml;
                    el.removeAttribute('aria-busy');
                    busy = false;
                    document.removeEventListener('click', onClick, true);
                    el.click();
                }
            }
            document.addEventListener('click', onClick, true);
        })();
    </script>
@endif
