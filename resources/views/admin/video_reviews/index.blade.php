@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="font-heading fw-bold text-dark m-0">Video Reviews</h2>
        <p class="text-muted m-0">Manage dynamic video testimonials displayed on the homepage.</p>
    </div>
    <a href="{{ route('admin.video-reviews.create') }}" class="btn btn-success px-4 py-2"><i class="bi bi-plus-lg me-1"></i> Add Video Review</a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table align-middle m-0">
            <thead class="bg-light text-muted" style="font-size: 0.85rem; font-family: 'DM Sans', sans-serif;">
                <tr>
                    <th scope="col" class="border-0 px-4">Reviewer Name</th>
                    <th scope="col" class="border-0">Video File</th>
                    <th scope="col" class="border-0">Linked Product</th>
                    <th scope="col" class="border-0">Status</th>
                    <th scope="col" class="border-0">Sort Order</th>
                    <th scope="col" class="border-0 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($videoReviews as $item)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-dark">{{ $item->reviewer_name }}</td>
                        <td class="py-3">
                            <a href="{{ asset($item->video_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-3"><i class="bi bi-play-circle me-1"></i> View Video</a>
                        </td>
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
                                <a href="{{ route('admin.video-reviews.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.video-reviews.delete', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this video review?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-play-btn display-4 d-block mb-3"></i>
                            No video reviews added yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($videoReviews->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $videoReviews->links() }}
        </div>
    @endif
</div>
@endsection
