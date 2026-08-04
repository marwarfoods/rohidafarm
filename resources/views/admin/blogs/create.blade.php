@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-pencil-square text-success me-2"></i>Write Blog Post</h1>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill"><i class="bi bi-arrow-left me-2"></i>Back to List</a>
</div>

<form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Form Left Column -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-file-earmark-post me-2"></i>Post Content</h5>

                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Post Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control rounded-3 @error('title') is-invalid @enderror" value="{{ old('title') }}" required placeholder="e.g. Health Benefits of Pure Gir Cow A2 Ghee">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label fw-bold">Short Excerpt / Intro (Displayed on List Page)</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control rounded-3" placeholder="Write a short summary (about 2-3 sentences) to capture the reader's attention...">{{ old('excerpt') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="editor" class="form-label fw-bold">Full Post Body / Content</label>
                    <textarea name="content" id="editor" rows="12" class="form-control rounded-3">{{ old('content') }}</textarea>
                </div>
            </div>

            <!-- SEO Settings Metadata -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-search me-2"></i>SEO Metadata Settings</h5>
                
                <div class="mb-3">
                    <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control rounded-3" value="{{ old('meta_title') }}" placeholder="Optimize search listing title (Leave blank to use post title)">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control rounded-3" placeholder="Brief SEO description to show under search engine snippets...">{{ old('meta_description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="keywords" class="form-label fw-bold">Keywords (Comma Separated)</label>
                    <input type="text" name="keywords" id="keywords" class="form-control rounded-3" value="{{ old('keywords') }}" placeholder="e.g. ghee benefits, a2 cow ghee, ayurvedic diet">
                </div>
            </div>
        </div>

        <!-- Sidebar / Right Column Options -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-sliders me-2"></i>Publishing Options</h5>

                <div class="mb-4">
                    <label for="blog_category_id" class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                    <select name="blog_category_id" id="blog_category_id" class="form-select rounded-3 @error('blog_category_id') is-invalid @enderror" required>
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="featuredImageInput" class="form-label fw-bold">Featured Image (Thumbnail)</label>
                    <input type="text" name="featured_image" id="featuredImageInput" class="form-control rounded-3 border p-2 shadow-none mb-2 @error('featured_image') is-invalid @enderror" placeholder="/uploads/products/image.jpg or URL" value="{{ old('featured_image') }}">
                    <div id="imagePreviewContainer" class="mt-2"></div>
                    <small class="text-muted d-block mt-2">Recommended: 800x500px. Pick from gallery, upload new, or input URL.</small>
                    @error('featured_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4 text-muted">

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input text-success fs-5 shadow-none" type="checkbox" role="switch" name="is_published" id="is_published" value="1" checked>
                    <label class="form-check-label fw-semibold text-dark ms-2 pt-1" for="is_published">Publish Immediately</label>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-success py-2.5 rounded-pill fw-bold text-uppercase"><i class="bi bi-save me-2"></i>Publish Post</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('admin_scripts')
<!-- Classic CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initMediaPicker) {
            initMediaPicker('#featuredImageInput', '#imagePreviewContainer', 'image');
        }

        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
<style>
    .ck-editor__editable_inline {
        min-height: 320px;
    }
</style>
@endpush
