<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'Admin') — RohidaFarm</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">

    {{-- Favicon --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">

    {{-- Admin Bundle (SCSS + JS compiled by Vite) --}}
    @vite(['resources/sass/admin.scss', 'resources/js/admin.js'])

    {{-- Media Picker (component lives in <body> below, so its CSS can't reach here via @push/@stack in time) --}}
    <link rel="stylesheet" href="{{ asset('admin/css/media-picker.css') }}?v={{ @filemtime(public_path('admin/css/media-picker.css')) }}">

    @stack('admin_styles')
</head>
<body>

    {{-- Admin Premium Preloader Overlay --}}
    <div id="adminPreloader" class="admin-preloader">
        <div class="spinner-container">
            <div class="brand-logo mb-3">
                <i class="bi bi-leaf-fill text-success" style="font-size: 3rem;"></i>
            </div>
            <div class="admin-spinner"></div>
            <p class="mt-3 text-muted fw-bold font-heading" style="font-size: 0.75rem; letter-spacing: 2px;">LOADING CONTROL PANEL...</p>
        </div>
    </div>

{{-- Flash messages as data tags (picked up by admin.js for toast notifications) --}}
@if(session('success'))
    <span data-flash-toast="success" class="d-none">{{ session('success') }}</span>
@endif
@if(session('error'))
    <span data-flash-toast="error" class="d-none">{{ session('error') }}</span>
@endif
@if(session('warning'))
    <span data-flash-toast="warning" class="d-none">{{ session('warning') }}</span>
@endif
@if(session('info'))
    <span data-flash-toast="info" class="d-none">{{ session('info') }}</span>
@endif
{{-- Validation failures previously failed silently (redirect back with no visible message) — surface them the same way. --}}
@if($errors->any())
    <span data-flash-toast="error" class="d-none">{{ $errors->first() }}</span>
@endif

{{-- App Shell --}}
<div class="admin-wrapper">

    {{-- ═══ Sidebar Component ═══ --}}
    <x-admin.sidebar />

    {{-- Mobile overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- ═══ Main Area ═══ --}}
    <div class="admin-main" id="adminMain">

        {{-- ═══ Topbar Component ═══ --}}
        <x-admin.topbar />

        {{-- ═══ Page Content ═══ --}}
        <main class="admin-content">
            @yield('admin_content')
        </main>

    </div>
</div>

{{-- Reusable Delete Modal --}}
<x-admin.delete-modal />

{{-- Media & Icon pickers (existing components) --}}
<x-media-picker />
<x-icon-picker />

{{-- Bootstrap JS (deferred) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

{{-- Media Picker JS --}}
<script src="{{ asset('admin/js/media-picker.js') }}?v={{ @filemtime(public_path('admin/js/media-picker.js')) }}"></script>

@stack('admin_scripts')
<script>
    window.addEventListener('load', function() {
        const preloader = document.getElementById('adminPreloader');
        if (preloader) {
            preloader.classList.add('fade-out');
            setTimeout(() => preloader.style.display = 'none', 400);
        }

        // Global Console Debugging with Emojis
        @if(session('error'))
            console.error('🚨 [Admin Debug - Error]:', {!! json_encode(session('error')) !!});
        @endif

        @if(session('success'))
            console.log('✅ [Admin Debug - Success]:', {!! json_encode(session('success')) !!});
        @endif

        @if(session('warning'))
            console.warn('⚠️ [Admin Debug - Warning]:', {!! json_encode(session('warning')) !!});
        @endif
    });
</script>
</body>
</html>
