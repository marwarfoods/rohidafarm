@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0">
        <i class="bi bi-patch-check-fill text-success me-2"></i>Certifications & Trust Marks
    </h1>
    <a href="{{ route('admin.certifications.create') }}" class="btn btn-success fw-bold text-uppercase"
       style="border-radius: 10px; font-size: 0.85rem; padding: 10px 20px;">
        <i class="bi bi-plus-lg me-1"></i> Add Certification
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger rounded-4 border-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
@endif

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <tr>
                    <th>Sort</th>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Cert. Number</th>
                    <th>Certificate Images</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.9rem;">
                @forelse($certifications as $cert)
                    <tr>
                        <td class="text-muted fw-bold">{{ $cert->sort_order }}</td>
                        <td>
                            @if($cert->logo_path)
                                <img src="{{ asset($cert->logo_path) }}" alt="{{ $cert->name }}" style="height: 40px; width: 40px; object-fit: contain; border-radius: 8px; border: 1px solid #eee;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $cert->name }}</td>
                        <td>{{ $cert->certificate_number ?? '—' }}</td>
                        <td>
                            @php $imgs = $cert->certificate_images ?? []; @endphp
                            @if(count($imgs) > 0)
                                <div class="d-flex gap-1 flex-wrap">
                                    @foreach(array_slice($imgs, 0, 3) as $img)
                                        <img src="{{ asset($img) }}" alt="cert" style="height: 36px; width: 36px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
                                    @endforeach
                                    @if(count($imgs) > 3)
                                        <span class="badge bg-secondary rounded-pill d-flex align-items-center">+{{ count($imgs) - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">No images</span>
                            @endif
                        </td>
                        <td>
                            @if($cert->is_active)
                                <span class="badge bg-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.certifications.edit', $cert->id) }}" class="btn btn-sm btn-outline-primary rounded-3 me-1">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.certifications.destroy', $cert->id) }}" class="d-inline" onsubmit="return confirm('Delete this certification?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-3"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-patch-check display-4 d-block mb-3 text-muted opacity-25"></i>
                            No certifications added yet. <a href="{{ route('admin.certifications.create') }}">Add your first one.</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
