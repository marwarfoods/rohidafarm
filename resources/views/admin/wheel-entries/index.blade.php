@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-life-preserver text-success me-2"></i>Wheel Popup Entries</h1>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
@endif

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile Number</th>
                    <th>Submitted On</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.9rem;">
                @forelse($entries as $entry)
                    <tr>
                        <td class="text-muted">{{ $loop->iteration + ($entries->currentPage() - 1) * $entries->perPage() }}</td>
                        <td class="fw-bold text-dark">{{ $entry->name }}</td>
                        <td>{{ $entry->mobile_number }}</td>
                        <td><small class="text-muted">{{ $entry->created_at->format('d M Y, h:i A') }}</small></td>
                        <td class="text-end">
                            <form action="{{ route('admin.wheel-entries.delete', $entry->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-3" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $entries->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
