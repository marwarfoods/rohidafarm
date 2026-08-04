@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-ticket-perforated text-success me-2"></i>Coupon Codes</h1>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-success fw-bold text-uppercase" style="border-radius: 10px; font-size: 0.85rem; padding: 10px 20px;">
        <i class="bi bi-plus-lg me-1"></i> Create Coupon
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
@endif

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Target</th>
                    <th>Used</th>
                    <th>Validity</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.9rem;">
                @forelse($coupons as $coupon)
                    <tr>
                        <td class="fw-bold text-dark">{{ $coupon->code }}</td>
                        <td>
                            @if($coupon->discount_type === 'percentage')
                                {{ $coupon->discount_value }}% OFF
                            @else
                                ₹{{ $coupon->discount_value }} OFF
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary text-capitalize">{{ $coupon->target_type }}</span>
                        </td>
                        <td>
                            <span class="fw-bold text-dark">{{ $coupon->usage_count }}</span> <span class="text-muted">/ {{ $coupon->usage_limit ?? '∞' }}</span>
                        </td>
                        <td>
                            <small class="text-muted d-block">From: {{ $coupon->active_from ? $coupon->active_from->format('d M Y') : 'Always' }}</small>
                            <small class="text-muted d-block">To: {{ $coupon->active_until ? $coupon->active_until->format('d M Y') : 'Never' }}</small>
                        </td>
                        <td>
                            @if($coupon->is_active)
                                <span class="badge bg-success rounded-pill px-3 py-2" style="font-size: 0.65rem;">ACTIVE</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size: 0.65rem;">INACTIVE</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-light border me-1 rounded-3" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('admin.coupons.delete', $coupon->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this coupon?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-3" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No coupons found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $coupons->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
