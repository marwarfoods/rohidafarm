{{-- ── Instagram Feed Marquee Section (Homepage Bottom) ── --}}
<section class="instagram-section py-5 position-relative overflow-hidden"
    style="background: url('{{ asset('images/vectors/bg10.png') }}') center center / cover no-repeat; border-top: 1px solid var(--border-color);">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(255, 255, 255, 0.55); z-index: 0; pointer-events: none;"></div>

    <div class="container py-2 position-relative" style="z-index: 1;">
        <!-- Section Header -->
        <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
            <span class="badge px-3 py-2 rounded-pill font-heading text-uppercase fw-semibold mb-2" 
                  style="background: linear-gradient(45deg, rgba(240,148,51,0.12), rgba(220,39,67,0.12), rgba(188,24,136,0.12)); color: #dc2743; border: 1px solid rgba(220,39,67,0.2); font-size: 0.75rem; letter-spacing: 1px;">
                <i class="bi bi-instagram me-1 fs-6"></i> @marwar_foods
            </span>
            <h2 class="font-heading fw-bold text-dark display-6 m-0">
                Follow Us On <span style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Instagram</span>
            </h2>
        </div>
    </div>

    @php
        $instaUrl = \App\Models\Setting::get('social_instagram') ?: 'https://www.instagram.com/marwar_foods/';
        if (!Str::contains($instaUrl, 'marwar_foods')) {
            $instaUrl = 'https://www.instagram.com/marwar_foods/';
        }

        $hasRealPosts = (isset($instaRow1) && $instaRow1->count()) || (isset($instaRow2) && $instaRow2->count());

        if ($hasRealPosts) {
            // Real posts uploaded from Admin > Instagram Feed
            $row1Posts = collect($instaRow1)->map(fn($p) => [
                'img' => asset($p->image_path),
                'caption' => $p->caption,
                'link' => $p->link_url ?: $instaUrl,
                'likes' => null,
                'comments' => null,
            ])->all();

            $row2Posts = collect($instaRow2)->map(fn($p) => [
                'img' => asset($p->image_path),
                'caption' => $p->caption,
                'link' => $p->link_url ?: $instaUrl,
                'likes' => null,
                'comments' => null,
            ])->all();
        } else {
            // Fallback demo content shown only until real posts are added in Admin
            $row1Posts = [
                ['img' => asset('images/baner-1.png'), 'likes' => '2.4k', 'comments' => '142', 'caption' => 'Golden A2 Bilona Ghee fresh batch! 💛', 'link' => $instaUrl],
                ['img' => asset('images/home-image-pc.png'), 'likes' => '3.1k', 'comments' => '209', 'caption' => 'Pure Tharparkar Cows grazing in pastures 🐄🌿', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=400&auto=format&fit=crop', 'likes' => '1.9k', 'comments' => '98', 'caption' => 'Traditional clay pot churning at 4 AM 🏺✨', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?q=80&w=400&auto=format&fit=crop', 'likes' => '1.5k', 'comments' => '76', 'caption' => 'Wood Pressed Kachi Ghani Mustard Oil 🌾💛', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?q=80&w=400&auto=format&fit=crop', 'likes' => '2.8k', 'comments' => '180', 'caption' => 'Raw Forest Honey straight from hives 🍯🐝', 'link' => $instaUrl],
                ['img' => asset('images/home-image-mobile.png'), 'likes' => '4.2k', 'comments' => '310', 'caption' => 'Eco-friendly glass jar packaging delivered! 📦❤️', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?q=80&w=400&auto=format&fit=crop', 'likes' => '3.8k', 'comments' => '245', 'caption' => 'Fresh organic spices harvested from farm 🌱⚡', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1628088062854-d1870b4553da?q=80&w=400&auto=format&fit=crop', 'likes' => '5.1k', 'comments' => '412', 'caption' => 'A2 Bilona Ghee on hot parathas! 🥞🔥', 'link' => $instaUrl]
            ];

            $row2Posts = [
                ['img' => 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?q=80&w=400&auto=format&fit=crop', 'likes' => '4.7k', 'comments' => '320', 'caption' => 'Sunlit morning at Marwar organic farm 🌄🌾', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1527153857715-3908f2bae5e8?q=80&w=400&auto=format&fit=crop', 'likes' => '2.9k', 'comments' => '174', 'caption' => 'Nourishing calves with love & care 🐮💛', 'link' => $instaUrl],
                ['img' => asset('images/baner-1.png'), 'likes' => '3.6k', 'comments' => '210', 'caption' => 'Danedar granular texture of pure Ghee 🧈✨', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1589927986089-35812388d1f4?q=80&w=400&auto=format&fit=crop', 'likes' => '1.8k', 'comments' => '88', 'caption' => 'Slow wood-fire flame clarification 🪵🔥', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?q=80&w=400&auto=format&fit=crop', 'likes' => '2.2k', 'comments' => '115', 'caption' => 'Earthenware storage for rich aroma 🏺🍯', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1471193945509-9ad0617afabf?q=80&w=400&auto=format&fit=crop', 'likes' => '3.3k', 'comments' => '201', 'caption' => 'Pure Groundnut Oil cold pressed naturally 🥜💛', 'link' => $instaUrl],
                ['img' => asset('images/home-image-pc.png'), 'likes' => '4.0k', 'comments' => '290', 'caption' => 'Pure Vedic tradition in every drop 🌿✨', 'link' => $instaUrl],
                ['img' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?q=80&w=400&auto=format&fit=crop', 'likes' => '2.5k', 'comments' => '150', 'caption' => '100% Chemical-free natural wellness 🍯🌿', 'link' => $instaUrl]
            ];
        }
    @endphp

    <!-- Marquee Container -->
    <div class="insta-marquee-wrapper overflow-hidden py-2 position-relative">
        
        <!-- Edge Fades -->
        <div class="position-absolute top-0 start-0 h-100" style="width: 60px; background: linear-gradient(to right, #fafbfc, rgba(250,251,252,0)); z-index: 10; pointer-events: none;"></div>
        <div class="position-absolute top-0 end-0 h-100" style="width: 60px; background: linear-gradient(to left, #fafbfc, rgba(250,251,252,0)); z-index: 10; pointer-events: none;"></div>

        <!-- Row 1: Left-to-Right Smooth Loop (Pauses ONLY when Row 1 is hovered) -->
        @if(count($row1Posts))
        <div class="marquee-row marquee-row-1 mb-3">
            <div class="marquee-track marquee-track-left">
                @foreach(array_merge($row1Posts, $row1Posts) as $post)
                    <div class="insta-marquee-item px-2">
                        <a href="{{ $post['link'] ?? $instaUrl }}" target="_blank" rel="noopener noreferrer" class="insta-square-card d-block position-relative rounded-4 overflow-hidden shadow-sm text-decoration-none group">
                            <img src="{{ $post['img'] }}" class="w-100 h-100 object-fit-cover insta-img" loading="lazy" decoding="async" alt="Marwar Foods Instagram Post">
                            <div class="insta-overlay position-absolute inset-0 d-flex flex-column justify-content-between p-3 text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-white bg-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.65rem;"><i class="bi bi-instagram me-1"></i> @marwar_foods</span>
                                </div>
                                @if(!empty($post['likes']) || !empty($post['comments']))
                                <div class="text-center my-auto">
                                    <div class="d-flex justify-content-center align-items-center gap-2 font-heading fw-bold" style="font-size: 0.85rem;">
                                        <span><i class="bi bi-heart-fill text-danger me-1"></i> {{ $post['likes'] }}</span>
                                        <span><i class="bi bi-chat-fill text-white me-1"></i> {{ $post['comments'] }}</span>
                                    </div>
                                </div>
                                @endif
                                @if(!empty($post['caption']))
                                <div>
                                    <p class="m-0 text-white-50 text-truncate" style="font-size: 0.72rem;">{{ $post['caption'] }}</p>
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Row 2: Right-to-Left Smooth Loop (Pauses ONLY when Row 2 is hovered) -->
        @if(count($row2Posts))
        <div class="marquee-row marquee-row-2">
            <div class="marquee-track marquee-track-right">
                @foreach(array_merge($row2Posts, $row2Posts) as $post)
                    <div class="insta-marquee-item px-2">
                        <a href="{{ $post['link'] ?? $instaUrl }}" target="_blank" rel="noopener noreferrer" class="insta-square-card d-block position-relative rounded-4 overflow-hidden shadow-sm text-decoration-none group">
                            <img src="{{ $post['img'] }}" class="w-100 h-100 object-fit-cover insta-img" loading="lazy" decoding="async" alt="Marwar Foods Instagram Post">
                            <div class="insta-overlay position-absolute inset-0 d-flex flex-column justify-content-between p-3 text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-white bg-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.65rem;"><i class="bi bi-instagram me-1"></i> @marwar_foods</span>
                                </div>
                                @if(!empty($post['likes']) || !empty($post['comments']))
                                <div class="text-center my-auto">
                                    <div class="d-flex justify-content-center align-items-center gap-2 font-heading fw-bold" style="font-size: 0.85rem;">
                                        <span><i class="bi bi-heart-fill text-danger me-1"></i> {{ $post['likes'] }}</span>
                                        <span><i class="bi bi-chat-fill text-white me-1"></i> {{ $post['comments'] }}</span>
                                    </div>
                                </div>
                                @endif
                                @if(!empty($post['caption']))
                                <div>
                                    <p class="m-0 text-white-50 text-truncate" style="font-size: 0.72rem;">{{ $post['caption'] }}</p>
                                </div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    <!-- Follow Button CTA -->
    <div class="text-center mt-4 mt-md-5" data-aos="fade-up">
        <a href="{{ $instaUrl }}" 
           target="_blank" rel="noopener noreferrer" 
           class="btn btn-lg rounded-pill px-5 py-3 font-heading fw-bold shadow-sm d-inline-flex align-items-center justify-content-center text-white" 
           style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); border: none; font-size: 0.95rem; letter-spacing: 0.5px; transition: transform 0.3s ease, box-shadow 0.3s ease;"
           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(220,39,67,0.35)';"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            <i class="bi bi-instagram me-2 fs-5"></i> Join @marwar_foods On Instagram
        </a>
    </div>
</section>

<style>
    /* 1:1 Square Ratio Card */
    .insta-square-card {
        width: 180px !important;
        height: 180px !important;
        aspect-ratio: 1 / 1 !important;
        flex-shrink: 0;
    }

    /* Overlay Hover Animation */
    .insta-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.4) 100%);
        opacity: 0;
        transition: opacity 0.35s ease;
        z-index: 2;
    }
    .insta-square-card:hover .insta-overlay {
        opacity: 1 !important;
    }
    .insta-square-card:hover .insta-img {
        transform: scale(1.08) !important;
    }
    .insta-img {
        transition: transform 0.5s ease;
    }

    /* Marquee Infinite Smooth Scrolling Animations */
    .marquee-track {
        display: flex;
        width: max-content;
        will-change: transform;
    }

    .marquee-track-left {
        animation: marquee-left-anim 35s linear infinite;
    }

    .marquee-track-right {
        animation: marquee-right-anim 35s linear infinite;
    }

    /* ONLY PAUSE THE HOVERED ROW (Row 1 or Row 2 independently) */
    .marquee-row-1:hover .marquee-track-left {
        animation-play-state: paused !important;
    }

    .marquee-row-2:hover .marquee-track-right {
        animation-play-state: paused !important;
    }

    @keyframes marquee-left-anim {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    @keyframes marquee-right-anim {
        0% { transform: translateX(-50%); }
        100% { transform: translateX(0); }
    }

    @media (max-width: 576px) {
        .insta-square-card {
            width: 130px !important;
            height: 130px !important;
        }
    }
</style>
