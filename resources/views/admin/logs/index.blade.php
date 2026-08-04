@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-shield-check text-success me-2"></i>Security Audit Trail</h1>
    
    <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all activity logs permanently?');">
        @csrf
        <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.8rem;"><i class="bi bi-trash me-1"></i> Clear Log Trail</button>
    </form>
</div>

<div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
    <!-- Search Logs form -->
    <form action="{{ route('admin.logs.index') }}" method="GET" class="mb-4 d-flex gap-2">
        <input type="text" name="search" class="form-control bg-light border p-2" placeholder="Search by action type, description or IP..." value="{{ request('search') }}" style="max-width: 350px; font-size: 0.85rem;">
        <button type="submit" class="btn btn-premium px-3 py-2 text-uppercase font-heading" style="font-size: 0.75rem;">Filter</button>
        <a href="{{ route('admin.logs.index') }}" class="btn btn-light border px-3 py-2 text-uppercase font-heading" style="font-size: 0.75rem;">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr class="text-muted" style="font-size: 0.8rem;">
                    <th scope="col">Timestamp</th>
                    <th scope="col">User</th>
                    <th scope="col">Action Code</th>
                    <th scope="col">Description</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">User Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="font-size: 0.85rem;">
                        <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        <td class="fw-semibold">{{ $log->user ? $log->user->name : 'Guest/System' }}</td>
                        <td><span class="badge bg-secondary-subtle text-dark border py-1 px-2" style="font-size: 0.75rem;">{{ $log->action }}</span></td>
                        <td class="text-muted">{{ $log->description }}</td>
                        <td>{{ $log->ip_address }}</td>
                        <td class="text-muted text-truncate" style="max-width: 200px;" title="{{ $log->user_agent }}">{{ $log->user_agent }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No activity logs recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
