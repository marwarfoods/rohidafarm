<footer class="footer pt-5 pb-4 position-relative overflow-hidden" style="font-family:'Plus Jakarta Sans',sans-serif; background-color: #FBF3E7;">
    <!-- Farm line-art background illustration -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image:url('{{ asset('images/footerbg.png') }}');background-size:cover;background-repeat:no-repeat;background-position:center;z-index:0;pointer-events:none;"></div>
    <!-- Soft overlay so text stays crisp on top of the bg image (same on mobile & desktop) -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(251, 243, 231, 0.55); z-index: 0; pointer-events: none;"></div>

    <div class="container position-relative" style="z-index:1;">
        <div class="row g-4 g-md-5">

            <!-- ── Brand Column ── -->
            <div class="col-12 col-md-6 col-lg-4">
                <a class="d-flex flex-column align-items-start mb-4" href="{{ route('home') }}">
                    @if(\App\Models\Setting::get('footer_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('footer_logo')) }}" alt="RohidaFarm Footer Logo"
                             width="230" height="130"
                             loading="lazy" decoding="async"
                             style="height: 130px; max-height: 150px; object-fit: contain;">
                    @else
                        <span class="fs-2 fw-black text-uppercase font-heading m-0" style="color: var(--primary-green); line-height:1;letter-spacing:-0.5px;">RohidaFarm</span>
                        <span class="text-uppercase fw-semibold" style="font-size:0.58rem;letter-spacing:3px;color:#8a7a68;">Pure & Traditional Organic</span>
                    @endif
                </a>
                <p class="mb-4" style="font-size:0.88rem;line-height:1.75;color:#5c5044;">
                    Reviving the ancient Bilona churning process and wood-pressed oil extractions — delivering premium pure organic ghee and natural ingredients straight to your kitchen table, with love from our farm.
                </p>
                <!-- Social Icons -->
                <div class="d-flex gap-3 mt-2">
                    @if(\App\Models\Setting::get('social_facebook'))
                        <a href="{{ \App\Models\Setting::get('social_facebook') }}" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Follow us on Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    @endif
                    @if(\App\Models\Setting::get('social_instagram'))
                        <a href="{{ \App\Models\Setting::get('social_instagram') }}" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Follow us on Instagram"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    @endif
                    @if(\App\Models\Setting::get('social_youtube'))
                        <a href="{{ \App\Models\Setting::get('social_youtube') }}" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Follow us on YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
                    @endif
                    @if(\App\Models\Setting::get('social_twitter'))
                        <a href="{{ \App\Models\Setting::get('social_twitter') }}" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Follow us on Twitter"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    @endif
                    @if(\App\Models\Setting::get('social_linkedin'))
                        <a href="{{ \App\Models\Setting::get('social_linkedin') }}" target="_blank" rel="noopener noreferrer" class="footer-social-icon" aria-label="Follow us on LinkedIn"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>


            <!-- ── Quick Links Column (Accordion on mobile, static on desktop) ── -->
            <div class="col-6 col-md-3 col-lg-2">
                <button class="footer-accordion-toggle d-flex d-md-none justify-content-between align-items-center w-100 border-0 bg-transparent p-0 mb-3"
                        type="button" data-bs-toggle="collapse" data-bs-target="#footerQuickLinks" aria-expanded="false" aria-controls="footerQuickLinks">
                    <h6 class="text-uppercase fw-bold font-heading m-0" style="letter-spacing:1.5px;font-size:0.75rem;color: var(--primary-green);">Quick Links</h6>
                    <i class="bi bi-chevron-down footer-accordion-icon" style="font-size:0.8rem;color: var(--primary-green);transition:transform .3s ease;"></i>
                </button>
                <h6 class="d-none d-md-block text-uppercase fw-bold font-heading mb-2" style="letter-spacing:1.5px;font-size:0.75rem;color: var(--primary-green);">Quick Links</h6>
                <div class="footer-heading-divider d-none d-md-block"></div>
                <div class="collapse d-md-block" id="footerQuickLinks">
                    <ul class="list-unstyled d-flex flex-column gap-3 mt-3" style="font-size:0.88rem;">
                        <li><a href="{{ route('about') }}" class="footer-link">Our Story</a></li>
                        <li><a href="{{ url('/#bilona-process') }}" class="footer-link">Bilona Process</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#labReportsModal" class="footer-link">Lab Reports</a></li>
                        <li><a href="{{ route('blogs.index') }}" class="footer-link">Healthy Blogs</a></li>
                        <li><a href="{{ route('contact') }}" class="footer-link">Contact Us</a></li>
                    </ul>
                </div>
            </div>

            <!-- ── Policy Pages Column (Dynamic — only Active ones) ── -->
            @php
                $footerPolicyPages = \App\Models\Page::whereIn('slug', ['privacy-policy','terms-conditions','refund-policy','shipping-policy'])
                                        ->where('is_active', true)
                                        ->orderByRaw("FIELD(slug, 'privacy-policy', 'terms-conditions', 'refund-policy', 'shipping-policy')")
                                        ->get();
                $policyRouteMap = [
                    'privacy-policy'   => 'privacy-policy',
                    'terms-conditions' => 'terms-conditions',
                    'refund-policy'    => 'refund-policy',
                    'shipping-policy'  => 'shipping-policy',
                ];
            @endphp
            @if($footerPolicyPages->isNotEmpty())
            <div class="col-6 col-md-3 col-lg-2">
                <button class="footer-accordion-toggle d-flex d-md-none justify-content-between align-items-center w-100 border-0 bg-transparent p-0 mb-3"
                        type="button" data-bs-toggle="collapse" data-bs-target="#footerPolicies" aria-expanded="false" aria-controls="footerPolicies">
                    <h6 class="text-uppercase fw-bold font-heading m-0" style="letter-spacing:1.5px;font-size:0.75rem;color: var(--primary-green);">Policies</h6>
                    <i class="bi bi-chevron-down footer-accordion-icon" style="font-size:0.8rem;color: var(--primary-green);transition:transform .3s ease;"></i>
                </button>
                <h6 class="d-none d-md-block text-uppercase fw-bold font-heading mb-2" style="letter-spacing:1.5px;font-size:0.75rem;color: var(--primary-green);">Policies</h6>
                <div class="footer-heading-divider d-none d-md-block"></div>
                <div class="collapse d-md-block" id="footerPolicies">
                    <ul class="list-unstyled d-flex flex-column gap-3 mt-3" style="font-size:0.88rem;">
                        @foreach($footerPolicyPages as $policyPage)
                            <li>
                                <a href="{{ url('/' . $policyPage->slug) }}" class="footer-link">
                                    {{ $policyPage->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- ── Contact + Newsletter Column (Accordion on mobile, static on desktop) ── -->
            <div class="col-12 col-md-12 col-lg-4">
                <button class="footer-accordion-toggle d-flex d-md-none justify-content-between align-items-center w-100 border-0 bg-transparent p-0 mb-3"
                        type="button" data-bs-toggle="collapse" data-bs-target="#footerCustomerSupport" aria-expanded="false" aria-controls="footerCustomerSupport">
                    <h6 class="text-uppercase fw-bold font-heading m-0" style="letter-spacing:1.5px;font-size:0.75rem;color: var(--primary-green);">Customer Support</h6>
                    <i class="bi bi-chevron-down footer-accordion-icon" style="font-size:0.8rem;color: var(--primary-green);transition:transform .3s ease;"></i>
                </button>
                <h6 class="d-none d-md-block text-uppercase fw-bold font-heading mb-2" style="letter-spacing:1.5px;font-size:0.75rem;color: var(--primary-green);">Customer Support</h6>
                <div class="footer-heading-divider d-none d-md-block"></div>
                <div class="collapse d-md-block" id="footerCustomerSupport">
                <div class="d-flex flex-column gap-3 mb-4 mt-3" style="font-size:0.88rem;">
                    <div class="d-flex align-items-start gap-2" style="color:#5c5044;">
                        <i class="bi bi-geo-alt-fill text-success mt-1" style="flex-shrink:0;"></i>
                        <span>{{ App\Models\Setting::get('contact_address') ?: 'Pune, Maharashtra, India' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:#5c5044;">
                        <i class="bi bi-telephone-fill text-success" style="flex-shrink:0;"></i>
                        <div>
                            @if(\App\Models\Setting::get('contact_mobile_1'))
                                <a href="tel:{{ App\Models\Setting::get('contact_mobile_1') }}" class="footer-link d-block">{{ App\Models\Setting::get('contact_mobile_1') }}</a>
                            @endif
                            @if(\App\Models\Setting::get('contact_mobile_2'))
                                <a href="tel:{{ App\Models\Setting::get('contact_mobile_2') }}" class="footer-link d-block mt-1">{{ App\Models\Setting::get('contact_mobile_2') }}</a>
                            @endif
                            @if(!\App\Models\Setting::get('contact_mobile_1') && !\App\Models\Setting::get('contact_mobile_2'))
                                <a href="tel:+919876543210" class="footer-link">+91 98765 43210</a>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="color:#5c5044;">
                        <i class="bi bi-envelope-fill text-success" style="flex-shrink:0;"></i>
                        <div>
                            @if(\App\Models\Setting::get('contact_email_1'))
                                <a href="mailto:{{ App\Models\Setting::get('contact_email_1') }}" class="footer-link d-block">{{ App\Models\Setting::get('contact_email_1') }}</a>
                            @endif
                            @if(\App\Models\Setting::get('contact_email_2'))
                                <a href="mailto:{{ App\Models\Setting::get('contact_email_2') }}" class="footer-link d-block mt-1">{{ App\Models\Setting::get('contact_email_2') }}</a>
                            @endif
                            @if(!\App\Models\Setting::get('contact_email_1') && !\App\Models\Setting::get('contact_email_2'))
                                <a href="mailto:care@rohidafarm.com" class="footer-link">care@rohidafarm.com</a>
                            @endif
                        </div>
                    </div>
                </div>
                </div>

                <!-- Trust badges row -->
                <div class="d-flex align-items-center gap-3 pt-3 flex-wrap" style="border-top: 1px dashed #d9c9ae;">
                    <div class="d-flex align-items-center gap-1" style="font-size:0.75rem;color:#5c5044;">
                        <i class="bi bi-shield-check text-success"></i> FSSAI Certified
                    </div>
                    <span class="d-none d-md-inline" style="color:#d9c9ae;">|</span>
                    <div class="d-none d-md-flex align-items-center gap-1" style="font-size:0.75rem;color:#5c5044;">
                        <i class="bi bi-truck text-success"></i> Free Shipping ₹499+
                    </div>
                    <span class="d-none d-md-inline" style="color:#d9c9ae;">|</span>
                    <div class="d-flex align-items-center gap-1" style="font-size:0.75rem;color:#5c5044;">
                        <i class="bi bi-arrow-return-left text-success"></i> 30-Day Returns
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-3 my-md-5" style="border-color:#d9c9ae;">

        <!-- Bottom Copyright Row -->
        <div class="row align-items-center gy-2">
            <div class="col-12 col-md-6 text-center text-md-start" style="font-size:0.82rem;color:#8a7a68;">
                &copy; {{ date('Y') }} <span style="color:#5c5044;">RohidaFarm</span>. All Rights Reserved.
            </div>
            <div class="col-12 col-md-6 text-center text-md-end" style="font-size:0.82rem;color:#8a7a68;">
                <span>Design & Developed by <a href="https://hinguland.com" target="_blank" class="text-decoration-none hover-gold" style="font-weight: 500; color: var(--primary-green);">Hinguland</a></span>
            </div>
        </div>
    </div>
</footer>
