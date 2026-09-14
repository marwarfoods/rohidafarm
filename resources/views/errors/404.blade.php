@php
    $seo = [
        'title' => '404 - Page Not Found | RohidaFarm',
        'description' => 'This page does not exist.',
        'canonical' => url()->current()
    ];
@endphp

@extends('layouts.app')

@section('content')
<div class="container py-5 my-md-5 text-center">
    <div class="py-5">
        <h1 class="fw-bold text-dark mb-2" style="font-size: clamp(3rem, 8vw, 4.5rem); letter-spacing: -1px; color: #1B5E20 !important;">
            404
        </h1>
        <p class="text-muted mb-4" style="font-size: 1.1rem;">
            This page does not exist.
        </p>
        <a href="{{ route('home') }}" class="btn btn-premium px-4 py-2 rounded-pill font-heading" style="font-size: 0.85rem;">
            Back to Home
        </a>
    </div>
</div>
@endsection
