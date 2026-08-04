@extends('layouts.admin')

@section('admin_content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-success text-decoration-none">Customers</a></li>
        <li class="breadcrumb-item active fw-semibold">Add New Customer</li>
    </ol>
</nav>

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="display-6 font-heading fw-bold m-0">
            <i class="bi bi-person-plus-fill text-success me-2"></i>Add New Customer
        </h1>
        <p class="text-muted mb-0 mt-1" style="font-size:0.88rem;">Create a new customer account manually.</p>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header">
                <h6 class="fw-bold m-0"><i class="bi bi-person me-2 text-success"></i>Customer Information</h6>
            </div>
            <div class="card-body p-4">

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.customers.store') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold" style="font-size:0.88rem;">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control rounded-3 @error('name') is-invalid @enderror"
                            placeholder="Enter customer's full name"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold" style="font-size:0.88rem;">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control rounded-3 @error('email') is-invalid @enderror"
                            placeholder="customer@example.com"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold" style="font-size:0.88rem;">Phone Number</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            class="form-control rounded-3 @error('phone') is-invalid @enderror"
                            placeholder="+91 XXXXX XXXXX"
                        >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold" style="font-size:0.88rem;">
                            Password <span class="text-danger">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control rounded-3 @error('password') is-invalid @enderror"
                            placeholder="Min. 8 characters"
                            required
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password Confirm --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold" style="font-size:0.88rem;">
                            Confirm Password <span class="text-danger">*</span>
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control rounded-3"
                            placeholder="Re-enter password"
                            required
                        >
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                            <i class="bi bi-check-circle me-2"></i>Create Customer
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
