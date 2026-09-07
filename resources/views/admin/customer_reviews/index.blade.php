@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold text-dark m-0">Customer Reviews</h2>
        <p class="text-muted m-0">Manage the testimonial slider shown on the homepage (photo on one side, review on the other).</p>
    </div>
    <a href="{{ route('admin.customer-reviews.create') }}" class="btn btn-success px-4 py-2"><i class="bi bi-plus-lg me-1"></i> Add Customer Review</a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle m-0">
            <thead class="bg-light text-muted" style="font-size: 0.85rem; font-family: 'DM Sans', sans-serif;">
                <tr>
                    <th scope="col" class="border-0 px-4">Image</th>
                    <th scope="col" class="border-0">Title</th>
                    <th scope="col" class="border-0">Rating</th>
                    <th scope="col" class="border-0">Customer</th>
                    <th scope="col" class="border-0">Linked Product</th>
                    <th scope="col" class="border-0">Status</th>
                    <th scope="col" class="border-0">Sort</th>
                    <th scope="col" class="border-0 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customerReviews as $item)
                    <tr>
                        <td class="px-4 py-3">
                            @if($item->image_path)
                                <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}" class="rounded-3 border object-fit-cover" style="width: 56px; height: 56px;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="py-3 fw-bold text-dark">{{ $item->title }}</td>
                        <td class="py-3 text-warning" style="letter-spacing: 1px;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $item->rating ? '-fill' : '' }}"></i>
                            @endfor
                        </td>
                        <td class="py-3">{{ $item->customer_name }}</td>
                        <td class="py-3">
                            @if($item->product)
                                <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2">{{ $item->product->name }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td class="py-3">
                            @if($item->is_active)
                                <span class="badge bg-success py-1 px-2">Active</span>
                            @else
                                <span class="badge bg-secondary py-1 px-2">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $item->sort_order }}</td>
                        <td class="py-3 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.customer-reviews.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.customer-reviews.delete', $item->id) }}" method="POST" onsubmit="return confirm('Delete this customer review?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-heart display-4 d-block mb-3"></i>
                            No customer reviews added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customerReviews->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $customerReviews->links() }}
        </div>
    @endif
</div>
@endsection
