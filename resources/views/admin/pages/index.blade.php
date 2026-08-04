@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-file-earmark-text text-success me-2"></i>Policy Pages Manager</h1>
    <span class="text-muted small">Manage your website's legal & policy pages content</span>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Tabs Navigation --}}
@php
    $activeTab = request('tab', array_key_first($predefinedPages));
@endphp

<ul class="nav nav-tabs border-bottom mb-4" id="pagesTabs" role="tablist">
    @foreach($predefinedPages as $slug => $label)
        @php $page = $pages->get($slug); @endphp
        <li class="nav-item" role="presentation">
            <a class="nav-link fw-semibold d-flex align-items-center gap-2 {{ $activeTab === $slug ? 'active' : '' }}"
               href="{{ route('admin.pages.index', ['tab' => $slug]) }}"
               id="tab-{{ $slug }}"
               style="font-size:0.88rem;">
                @if($page && $page->is_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.65rem;">Active</span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.65rem;">Draft</span>
                @endif
                {{ $label }}
            </a>
        </li>
    @endforeach
</ul>

{{-- Active Tab Content --}}
@if(isset($predefinedPages[$activeTab]))
    @php $page = $pages->get($activeTab); $label = $predefinedPages[$activeTab]; @endphp
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-file-earmark-richtext me-2 text-success"></i>{{ $label }}</h5>
                <small class="text-muted">Slug: <code>/{{ $activeTab }}</code></small>
            </div>
            <div class="d-flex align-items-center gap-3">
                @if($page && $page->is_active)
                    <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Published</span>
                @else
                    <span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-pencil-square me-1"></i>Draft</span>
                @endif
                <a href="{{ route('admin.pages.edit', $activeTab) }}" class="btn btn-success px-4 py-2 rounded-pill">
                    <i class="bi bi-pencil-square me-2"></i>Edit Page Content
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            @if($page && $page->content)
                <div class="border rounded-3 p-4 bg-light" style="max-height:400px;overflow-y:auto;font-size:0.9rem;line-height:1.7;">
                    {!! $page->content !!}
                </div>
                <div class="d-flex gap-4 mt-3" style="font-size:0.82rem;color:#666;">
                    <span><i class="bi bi-calendar3 me-1"></i>Last Updated: {{ $page->updated_at->format('d M Y, h:i A') }}</span>
                    @if($page->meta_title)
                        <span><i class="bi bi-search me-1"></i>Meta: {{ $page->meta_title }}</span>
                    @endif
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-earmark-x display-4 d-block mb-3 opacity-25"></i>
                    <p class="mb-3">No content has been added for this page yet.</p>
                    <a href="{{ route('admin.pages.edit', $activeTab) }}" class="btn btn-outline-success rounded-pill px-4">
                        <i class="bi bi-plus-lg me-2"></i>Add Content Now
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- All Pages Quick Overview --}}
    <div class="row g-3 mt-3">
        @foreach($predefinedPages as $slug => $lbl)
            @php $p = $pages->get($slug); @endphp
            <div class="col-md-3">
                <div class="card border-0 rounded-3 shadow-sm h-100 {{ $activeTab === $slug ? 'border-success border-2' : '' }}">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <i class="bi bi-file-earmark-text fs-4 text-success opacity-75"></i>
                            @if($p && $p->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.65rem;">Active</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.65rem;">Draft</span>
                            @endif
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size:0.85rem;">{{ $lbl }}</h6>
                        <small class="text-muted mb-3">/{{ $slug }}</small>
                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('admin.pages.edit', $slug) }}" class="btn btn-sm btn-outline-success flex-grow-1">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="{{ url('/' . $slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
