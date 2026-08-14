@extends('layouts.admin')

@section('admin_content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-success text-decoration-none">Customers</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer->id) }}" class="text-success text-decoration-none">{{ $customer->name }}</a></li>
        <li class="breadcrumb-item active fw-semibold">Edit</li>
    </ol>
</nav>

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="display-6 font-heading fw-bold m-0">
            <i class="bi bi-pencil-square text-success me-2"></i>Edit Customer
        </h1>
        <p class="text-muted mb-0 mt-1" style="font-size:0.88rem;">
            Editing: <strong>{{ $customer->name }}</strong> &mdash; ID #{{ $customer->id }}
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-eye me-1"></i>View Profile
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header">
                <h6 class="fw-bold m-0"><i class="bi bi-person-gear me-2 text-success"></i>Update Customer Details</h6>
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

                <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold" style="font-size:0.88rem;">
                            Full Name <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $customer->name) }}"
                            class="form-control rounded-3 @error('name') is-invalid @enderror"
                            placeholder="Full name"
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
                            value="{{ old('email', $customer->email) }}"
                            class="form-control rounded-3 @error('email') is-invalid @enderror"
                            placeholder="Email"
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
                            value="{{ old('phone', $customer->phone) }}"
                            class="form-control rounded-3 @error('phone') is-invalid @enderror"
                            placeholder="Phone number"
                        >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role Selection --}}
                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-semibold" style="font-size:0.88rem;">User Role</label>
                        <select name="role_id" id="role_id" class="form-select rounded-3">
                            <option value="customer" {{ !$customer->roles->count() ? 'selected' : '' }}>Customer (Default)</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $customer->roles->contains('id', $role->id) ? 'selected' : '' }}>
                                    {{ $role->display_name }} ({{ $role->name }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Assigning an administrative role gives control panel access based on the role's permissions.</small>
                    </div>


                    <hr class="my-4">
                    <p class="text-muted mb-3" style="font-size:0.85rem;">
                        <i class="bi bi-info-circle me-1"></i>
                        Leave password fields blank to keep the current password.
                    </p>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold" style="font-size:0.88rem;">New Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control rounded-3 @error('password') is-invalid @enderror"
                            placeholder="Leave blank to keep current"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password Confirm --}}
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-semibold" style="font-size:0.88rem;">Confirm New Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control rounded-3"
                            placeholder="Re-enter new password"
                        >
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                            <i class="bi bi-check-circle me-2"></i>Update Customer
                        </button>
                        <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-secondary rounded-3 px-3">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
