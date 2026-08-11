@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0">
        <i class="bi bi-instagram text-danger me-2"></i>Instagram Feed Gallery
    </h1>
    <a href="{{ route('admin.instagram-feed.create') }}" class="btn btn-success fw-bold text-uppercase"
       style="border-radius: 10px; font-size: 0.85rem; padding: 10px 20px;">
        <i class="bi bi-plus-lg me-1"></i> Add Instagram Post
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
                    <th>Sort</th>
                    <th>Image</th>
                    <th>Row</th>
                    <th>Caption</th>
                    <th>Redirect URL</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.9rem;">
                @forelse($posts as $post)
                    <tr>
                        <td class="text-muted fw-bold">{{ $post->sort_order }}</td>
                        <td>
                            <img src="{{ asset($post->image_path) }}" alt="Instagram Post" style="height: 60px; width: 60px; object-fit: cover; border-radius: 10px; border: 1px solid #eee;">
                        </td>
                        <td><span class="badge bg-info-subtle text-info-emphasis rounded-pill">Row {{ $post->row }}</span></td>
                        <td class="fw-bold">{{ $post->caption ?: '—' }}</td>
                        <td>
                            @if($post->link_url)
                                <a href="{{ $post->link_url }}" target="_blank" class="text-primary text-decoration-none text-truncate d-inline-block" style="max-width: 220px;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>{{ $post->link_url }}
                                </a>
                            @else
                                <span class="text-muted small">Default (Site Instagram)</span>
                            @endif
                        </td>
                        <td>
                            @if($post->is_active)
                                <span class="badge bg-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.instagram-feed.edit', $post->id) }}" class="btn btn-sm btn-outline-primary rounded-3 me-1">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.instagram-feed.destroy', $post->id) }}" class="d-inline" onsubmit="return confirm('Delete this Instagram post?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-instagram display-4 d-block mb-3 text-muted opacity-25"></i>
                            No Instagram posts added yet. <a href="{{ route('admin.instagram-feed.create') }}">Add your first Instagram post.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
