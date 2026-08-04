@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            @include('customer.partials.sidebar')

            <!-- Panel Contents -->
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-4" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif

                <div class="row g-4">
                    <!-- Update Profile details -->
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Update Details</h4>
                            
                            <form action="{{ route('customer.profile.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Full Name *</label>
                                    <input type="text" name="name" class="form-control bg-light border p-2" value="{{ $user->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Email Address *</label>
                                    <input type="email" name="email" class="form-control bg-light border p-2" value="{{ $user->email }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Mobile Number *</label>
                                    <input type="text" name="phone" class="form-control bg-light border p-2" value="{{ $user->phone }}" required>
                                </div>
                                <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Save Changes</button>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="col-md-6">
                        <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Change Password</h4>
                            
                            <form action="{{ route('customer.profile.password') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Current Password *</label>
                                    <input type="password" name="current_password" class="form-control bg-light border p-2" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">New Password *</label>
                                    <input type="password" name="password" class="form-control bg-light border p-2" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Confirm New Password *</label>
                                    <input type="password" name="password_confirmation" class="form-control bg-light border p-2" required>
                                </div>
                                <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Update Password</button>
                            </form>
                        </div>
                    </div>

                    <!-- Shipping Address Manager -->
                    <div class="col-12">
                        <div class="bg-white p-4 rounded-4 shadow-sm border animate-fade-in" style="border-color: var(--border-color) !important;">
                            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Address Book Manager</h4>
                            
                            <div class="row g-4">
                                <!-- List current addresses -->
                                <div class="col-md-7">
                                    <div class="row g-3">
                                        @forelse($user->addresses as $addr)
                                            <div class="col-12">
                                                <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <strong>{{ $addr->name }}</strong>
                                                            <span class="badge bg-secondary text-uppercase" style="font-size: 0.6rem;">{{ $addr->type }}</span>
                                                            @if($addr->is_default)
                                                                <span class="badge bg-success text-uppercase" style="font-size: 0.6rem;">Default</span>
                                                            @endif
                                                        </div>
                                                        <p class="text-muted m-0" style="font-size: 0.85rem;">{{ $addr->address_line1 }}</p>
                                                        <p class="text-muted m-0" style="font-size: 0.85rem;">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}</p>
                                                        <p class="text-muted m-0 mt-2" style="font-size: 0.8rem;"><i class="bi bi-telephone text-success me-1"></i>{{ $addr->phone }}</p>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-column gap-1 align-items-end">
                                                        <button type="button" class="btn btn-sm text-primary px-2 py-0 border-0" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $addr->id }}"><i class="bi bi-pencil me-1"></i> Edit</button>
                                                        @if(!$addr->is_default)
                                                            <form action="{{ route('customer.addresses.default', $addr->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-success px-2 py-0 border-0" style="font-size: 0.75rem;">Set Default</button>
                                                            </form>
                                                            <form action="{{ route('customer.addresses.delete', $addr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this address?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm text-danger px-2 py-0 border-0" style="font-size: 0.75rem;"><i class="bi bi-trash"></i> Delete</button>
                                                            </form>
                                                        @endif
                                                    </div>

                                                    <!-- Edit Address Modal -->
                                                    <div class="modal fade" id="editAddressModal{{ $addr->id }}" tabindex="-1" aria-labelledby="editAddressModalLabel{{ $addr->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content rounded-4 border-0 shadow-lg">
                                                                <div class="modal-header border-bottom">
                                                                    <h5 class="modal-title font-heading fw-bold text-dark" id="editAddressModalLabel{{ $addr->id }}">Edit Address</h5>
                                                                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <form action="{{ route('customer.addresses.update', $addr->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body p-4">
                                                                        <div class="mb-2">
                                                                            <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">Recipient Name</label>
                                                                            <input type="text" name="name" class="form-control bg-light border p-2" value="{{ $addr->name }}" required style="font-size: 0.85rem;">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">Phone Number</label>
                                                                            <input type="text" name="phone" class="form-control bg-light border p-2" value="{{ $addr->phone }}" required style="font-size: 0.85rem;">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">Address Line 1</label>
                                                                            <input type="text" name="address_line1" class="form-control bg-light border p-2" value="{{ $addr->address_line1 }}" required style="font-size: 0.85rem;">
                                                                        </div>
                                                                        <div class="mb-2">
                                                                            <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">Address Line 2 (Optional)</label>
                                                                            <input type="text" name="address_line2" class="form-control bg-light border p-2" value="{{ $addr->address_line2 }}" style="font-size: 0.85rem;">
                                                                        </div>
                                                                        <div class="row g-2 mb-3">
                                                                            <div class="col-4">
                                                                                <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">City</label>
                                                                                <input type="text" name="city" class="form-control bg-light border p-2" value="{{ $addr->city }}" required style="font-size: 0.85rem;">
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">State</label>
                                                                                <input type="text" name="state" class="form-control bg-light border p-2" value="{{ $addr->state }}" required style="font-size: 0.85rem;">
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <label class="form-label text-muted mb-1" style="font-size: 0.75rem;">Pin Code</label>
                                                                                <input type="text" name="postal_code" class="form-control bg-light border p-2" value="{{ $addr->postal_code }}" required style="font-size: 0.85rem;">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer border-0 p-3 pt-0">
                                                                        <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Update Address</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-muted py-4 text-center">No shipping addresses configured.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Add new Address -->
                                <div class="col-md-5">
                                    <div class="p-3 bg-light rounded-4 border">
                                        <h5 class="fw-bold font-heading text-dark border-bottom pb-2 mb-3">Add New Address</h5>
                                        <form action="{{ route('customer.addresses.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="type" value="shipping">
                                            <div class="mb-2">
                                                <input type="text" name="name" class="form-control bg-white border p-2" placeholder="Recipient Full Name *" required style="font-size: 0.85rem;">
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" name="phone" class="form-control bg-white border p-2" placeholder="Recipient Mobile Number *" required style="font-size: 0.85rem;">
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" name="address_line1" class="form-control bg-white border p-2" placeholder="Street Address / House No. *" required style="font-size: 0.85rem;">
                                            </div>
                                            <div class="mb-2">
                                                <input type="text" name="address_line2" class="form-control bg-white border p-2" placeholder="Landmark / Area (Optional)" style="font-size: 0.85rem;">
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-4">
                                                    <input type="text" name="city" class="form-control bg-white border p-2" placeholder="City *" required style="font-size: 0.85rem;">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" name="state" class="form-control bg-white border p-2" placeholder="State *" required style="font-size: 0.85rem;">
                                                </div>
                                                <div class="col-4">
                                                    <input type="text" name="postal_code" class="form-control bg-white border p-2" placeholder="Pin *" required style="font-size: 0.85rem;">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Save Address</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
