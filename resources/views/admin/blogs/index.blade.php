@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-journal-text text-success me-2"></i>Blogs & Articles</h1>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-success px-4 py-2 rounded-pill"><i class="bi bi-plus-lg me-2"></i>Write New Post</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold text-dark m-0"><i class="bi bi-list-stars me-2"></i>Published Posts</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="text-muted" style="font-size: 0.8rem;">
                    <th scope="col">Thumbnail</th>
                    <th scope="col">Title</th>
                    <th scope="col">Category</th>
                    <th scope="col">Status</th>
                    <th scope="col">Views</th>
                    <th scope="col">Published At</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blogs as $blog)
                    <tr style="font-size: 0.88rem;">
                        <td>
                            @if($blog->featured_image)
                                <img src="{{ asset($blog->featured_image) }}" class="object-fit-cover" style="width: 60px; height: 60px;" alt="{{ $blog->title }}">
                            @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 60px; font-size: 0.8rem;">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark d-block" style="max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $blog->title }}</strong>
                            <span class="text-muted d-block" style="font-size: 0.75rem;">Author: {{ $blog->author_name }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border">{{ $blog->category->name }}</span>
                        </td>
                        <td>
                            @if($blog->is_published)
                                <span class="badge bg-success-subtle text-success border-success-subtle"><i class="bi bi-check2-circle me-1"></i>Published</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border-warning-subtle"><i class="bi bi-pencil-square me-1"></i>Draft</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark fw-bold"><i class="bi bi-eye me-1 text-muted"></i>{{ $blog->view_count }}</span>
                        </td>
                        <td class="text-muted">
                            {{ $blog->published_at ? $blog->published_at->format('d M Y') : '-' }}
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-outline-success px-3 py-1.5"><i class="bi bi-pencil-square me-1"></i>Edit</a>
                                <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1.5"><i class="bi bi-trash me-1"></i>Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x display-6 d-block mb-3 text-muted-50"></i>
                            No blog posts have been written yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($blogs->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $blogs->links() }}
        </div>
    @endif
</div>
@endsection
