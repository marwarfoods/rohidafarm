@php
    $rawAboutVideo = \App\Models\Setting::get('home_story_video') ?: 'https://res.cloudinary.com/debmxiph/video/upload/v1789550013/about-us.mp4';
    $aboutVideo = str_starts_with($rawAboutVideo, 'http') ? $rawAboutVideo : asset($rawAboutVideo);
@endphp
<!-- Making Of Video Section -->
<section class="about-video-section py-4 py-md-5">
    <div class="container" data-aos="fade-up">
        <div class="about-video-wrapper w-100 mx-auto" id="aboutVideoWrapper">
            <video id="aboutFeatureVideo" 
                   class="w-100" 
                   muted 
                   loop 
                   playsinline 
                   preload="metadata"
                   style="width: 100%; height: auto; max-height: 620px; display: block; object-fit: cover; border-radius: 24px;">
                <source src="{{ $aboutVideo }}" type="video/mp4">
                Your browser does not support HTML5 video.
            </video>
        </div>
    </div>
</section>
