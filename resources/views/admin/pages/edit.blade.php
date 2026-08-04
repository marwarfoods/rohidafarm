@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="display-6 font-heading fw-bold m-0">
            <i class="bi bi-pencil-square text-success me-2"></i>Edit — {{ $label }}
        </h1>
        <small class="text-muted">Frontend URL: <a href="{{ url('/' . $page->slug) }}" target="_blank" class="text-success">/{{ $page->slug }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.7rem;"></i></a></small>
    </div>
    <a href="{{ route('admin.pages.index', ['tab' => $page->slug]) }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
        <i class="bi bi-arrow-left me-2"></i>Back to Pages
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3 border-0 mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Validation errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.pages.update', $page->slug) }}" method="POST">
    @csrf
    <div class="row g-4">

        {{-- ── Main Content Editor (Left) ── --}}
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-file-richtext me-2 text-success"></i>Page Content
                </h5>

                <div class="mb-3">
                    <label for="editor" class="form-label fw-bold">Page Body Content <span class="text-danger">*</span></label>
                    <textarea name="content" id="editor" rows="16" class="form-control rounded-3">{{ old('content', $page->content) }}</textarea>
                    <small class="text-muted">Use the rich text editor to format your content with headings, lists, bold, italic, links, etc.</small>
                </div>
            </div>

            {{-- SEO Metadata --}}
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-search me-2 text-success"></i>SEO Settings
                </h5>

                <div class="mb-3">
                    <label for="meta_title" class="form-label fw-bold">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control rounded-3"
                           value="{{ old('meta_title', $page->meta_title) }}"
                           placeholder="e.g. Privacy Policy — RohidaFarm">
                    <small class="text-muted">Recommended: 50–60 characters. Leave blank to use page title.</small>
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-bold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control rounded-3"
                              placeholder="Brief description shown in search engine results...">{{ old('meta_description', $page->meta_description) }}</textarea>
                    <small class="text-muted">Recommended: 120–160 characters.</small>
                </div>

                <div class="mb-3">
                    <label for="keywords" class="form-label fw-bold">Keywords <span class="text-muted fw-normal">(comma separated)</span></label>
                    <input type="text" name="keywords" id="keywords" class="form-control rounded-3"
                           value="{{ old('keywords', $page->keywords) }}"
                           placeholder="e.g. privacy policy, data protection, rohidafarm">
                </div>
            </div>
        </div>

        {{-- ── Right Sidebar Options ── --}}
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="bi bi-sliders me-2 text-success"></i>Publishing Options
                </h5>

                {{-- Status Toggle --}}
                <div class="rounded-3 p-3 mb-4" style="background:#f8fdf9;border:1px solid #d4edda;">
                    <label class="form-label fw-bold mb-2">Page Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input fs-5 shadow-none" type="checkbox"
                               role="switch" name="is_active" id="is_active"
                               value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark ms-2 pt-1" for="is_active">
                            <span id="statusLabel">{{ $page->is_active ? 'Active (Visible on frontend)' : 'Draft (Hidden from footer)' }}</span>
                        </label>
                    </div>
                    <div class="mt-2 p-2 rounded-2" id="statusInfo"
                         style="font-size:0.78rem;background:{{ $page->is_active ? '#e8f5e9' : '#fff8e1' }};color:{{ $page->is_active ? '#2e7d32' : '#f57f17' }};">
                        @if($page->is_active)
                            <i class="bi bi-check-circle me-1"></i> This page is publicly visible and linked in the footer.
                        @else
                            <i class="bi bi-eye-slash me-1"></i> This page is hidden. It won't appear in the footer.
                        @endif
                    </div>
                </div>

                {{-- Page Info --}}
                <div class="border rounded-3 p-3 mb-4" style="font-size:0.82rem;">
                    <div class="text-muted mb-2 fw-semibold text-uppercase" style="font-size:0.7rem;letter-spacing:1px;">Page Info</div>
                    <div class="d-flex flex-column gap-2">
                        <div><i class="bi bi-link-45deg text-success me-1"></i><strong>Slug:</strong> /{{ $page->slug }}</div>
                        <div><i class="bi bi-calendar3 text-success me-1"></i><strong>Created:</strong> {{ $page->created_at->format('d M Y') }}</div>
                        <div><i class="bi bi-clock-history text-success me-1"></i><strong>Updated:</strong> {{ $page->updated_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>

                {{-- Quick page links --}}
                <div class="border rounded-3 p-3 mb-4" style="font-size:0.82rem;">
                    <div class="text-muted mb-2 fw-semibold text-uppercase" style="font-size:0.7rem;letter-spacing:1px;">Other Pages</div>
                    @foreach(['privacy-policy' => 'Privacy Policy', 'terms-conditions' => 'Terms & Conditions', 'refund-policy' => 'Refund & Return Policy', 'shipping-policy' => 'Shipping Policy'] as $slug => $lbl)
                        @if($slug !== $page->slug)
                            <a href="{{ route('admin.pages.edit', $slug) }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark py-1 border-bottom" style="font-size:0.82rem;">
                                <i class="bi bi-file-earmark-text text-muted"></i> {{ $lbl }}
                            </a>
                        @endif
                    @endforeach
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success py-2 rounded-pill fw-bold">
                        <i class="bi bi-save me-2"></i>Save Page
                    </button>
                    <a href="{{ url('/' . $page->slug) }}" target="_blank" class="btn btn-outline-secondary py-2 rounded-pill">
                        <i class="bi bi-eye me-2"></i>Preview Live Page
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('admin_scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // CKEditor with full toolbar for page content
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'link', 'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    ]
                }
            })
            .catch(error => console.error(error));

        // Live status toggle label update
        const toggle = document.getElementById('is_active');
        const statusLabel = document.getElementById('statusLabel');
        const statusInfo = document.getElementById('statusInfo');

        if (toggle) {
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    statusLabel.textContent = 'Active (Visible on frontend)';
                    statusInfo.style.background = '#e8f5e9';
                    statusInfo.style.color = '#2e7d32';
                    statusInfo.innerHTML = '<i class="bi bi-check-circle me-1"></i> This page is publicly visible and linked in the footer.';
                } else {
                    statusLabel.textContent = 'Draft (Hidden from footer)';
                    statusInfo.style.background = '#fff8e1';
                    statusInfo.style.color = '#f57f17';
                    statusInfo.innerHTML = '<i class="bi bi-eye-slash me-1"></i> This page is hidden. It won\'t appear in the footer.';
                }
            });
        }
    });
</script>
<style>
    .ck-editor__editable_inline { min-height: 380px; }
</style>
@endpush
