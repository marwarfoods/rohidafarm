@extends('layouts.app')

@section('content')
<div class="about-page">
    @include('frontend.pages.about.hero')
    @include('frontend.pages.about.intro')
    @include('frontend.pages.about.story')
    @include('frontend.pages.about.difference')
    @include('frontend.pages.about.pillars')
    @include('frontend.pages.about.why-us')
    {{-- Video section hidden for now --}}
    {{-- @include('frontend.pages.about.video') --}}
    @include('frontend.pages.about.founder')
</div>
@endsection

@push('styles')
    @vite(['resources/sass/about.scss'])
@endpush

@push('scripts')
    @vite(['resources/js/about.js'])
@endpush
