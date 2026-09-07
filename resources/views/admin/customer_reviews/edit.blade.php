@extends('layouts.admin')

@section('admin_content')
<div class="mb-4">
    <a href="{{ route('admin.customer-reviews.index') }}" class="btn btn-link text-success p-0 text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Listing</a>
    <h2 class="font-heading fw-bold text-dark mt-2">Edit Customer Review</h2>
    <p class="text-muted m-0">Update "{{ $customerReview->title }}".</p>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
    <form action="{{ route('admin.customer-reviews.update', $customerReview->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.customer_reviews._form', ['customerReview' => $customerReview])
        <div class="col-12 mt-4 border-top pt-4">
            <button type="submit" class="btn btn-success px-5 py-3 rounded-pill text-uppercase fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">Update Customer Review</button>
        </div>
    </form>
</div>
@endsection

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.initMediaPicker === 'function') {
            window.initMediaPicker('#imagePathInput', '#imagePreview', 'image');
        }
    });

    function previewCustomerReviewImage(input) {
        const container = document.querySelector('#imagePreview');
        if (!container || !input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            container.innerHTML = `<img src="${e.target.result}" class="rounded-3 img-fluid border" style="max-height: 160px; object-fit: contain;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>
@endpush
