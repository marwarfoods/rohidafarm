@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-envelope-paper text-success me-2"></i>Contact Form Entries</h1>
        <p class="text-muted mb-0 small">Review and respond to messages submitted by visitors on the Contact Us page.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
@endif

{{-- Search and Filter Bar --}}
<div class="card border-0 rounded-4 shadow-sm bg-white p-3 mb-4">
    <form action="{{ route('admin.contact-inquiries.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0 shadow-none" placeholder="Search by name, email, phone or message..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select bg-light shadow-none">
                <option value="">All Statuses</option>
                <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread Only</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-success px-4 rounded-pill fw-semibold" style="font-size: 0.85rem;"><i class="bi bi-filter me-1"></i> Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.contact-inquiries.index') }}" class="btn btn-outline-secondary px-3 rounded-pill" style="font-size: 0.85rem;">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Sender Name</th>
                    <th>Email & Phone</th>
                    <th>Subject & Message</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.88rem;">
                @forelse($inquiries as $item)
                    <tr class="{{ $item->status === 'unread' ? 'table-light fw-semibold' : '' }}" id="inquiryRow_{{ $item->id }}">
                        <td class="text-muted">{{ $loop->iteration + ($inquiries->currentPage() - 1) * $inquiries->perPage() }}</td>
                        <td>
                            @if($item->status === 'unread')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill" id="badge_{{ $item->id }}">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i>Unread
                                </span>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" id="badge_{{ $item->id }}">
                                    Read
                                </span>
                            @endif
                        </td>
                        <td class="fw-bold text-dark">
                            {{ $item->name }}
                        </td>
                        <td>
                            <div><a href="mailto:{{ $item->email }}" class="text-decoration-none text-dark fw-medium">{{ $item->email }}</a></div>
                            @if($item->phone)
                                <small class="text-muted"><a href="tel:{{ $item->phone }}" class="text-decoration-none text-muted"><i class="bi bi-telephone me-1"></i>{{ $item->phone }}</a></small>
                            @endif
                        </td>
                        <td style="max-width: 320px;">
                            @if($item->subject)
                                <div class="text-dark fw-semibold text-truncate">{{ $item->subject }}</div>
                            @endif
                            <div class="text-muted text-truncate" style="font-size: 0.82rem;">{{ $item->message }}</div>
                        </td>
                        <td>
                            <small class="text-muted">{{ $item->created_at->format('d M Y, h:i A') }}</small>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary border rounded-3 btn-view-inquiry"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-email="{{ $item->email }}"
                                    data-phone="{{ $item->phone ?? 'N/A' }}"
                                    data-subject="{{ $item->subject ?? 'No Subject' }}"
                                    data-message="{{ $item->message }}"
                                    data-ip="{{ $item->ip_address ?? 'N/A' }}"
                                    data-date="{{ $item->created_at->format('d M Y, h:i A') }}"
                                    data-status="{{ $item->status }}"
                                    title="View Full Message">
                                    <i class="bi bi-eye"></i>
                                </button>
                                
                                <form action="{{ route('admin.contact-inquiries.delete', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this contact submission?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border rounded-3" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            No contact inquiries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $inquiries->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- View Inquiry Modal --}}
<div class="modal fade" id="inquiryDetailModal" tabindex="-1" aria-labelledby="inquiryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="inquiryDetailModalLabel">
                    <i class="bi bi-envelope-open text-success me-2"></i>Inquiry Details
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Sender Name</label>
                        <div class="fw-bold fs-6 text-dark" id="modalSenderName"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Submitted Date</label>
                        <div class="text-dark" id="modalDate"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Email Address</label>
                        <div><a href="#" id="modalEmailLink" class="text-success fw-bold text-decoration-none"></a></div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold">Phone Number</label>
                        <div><a href="#" id="modalPhoneLink" class="text-dark fw-bold text-decoration-none"></a></div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small text-uppercase fw-bold">Subject</label>
                        <div class="fw-semibold text-dark fs-6" id="modalSubject"></div>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3 border">
                    <label class="text-muted small text-uppercase fw-bold mb-2 d-block">Message Content</label>
                    <p class="text-dark mb-0" id="modalMessage" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"></p>
                </div>

                <div class="mt-3 text-muted small">
                    <i class="bi bi-geo text-secondary me-1"></i>IP Address: <span id="modalIp"></span>
                </div>
            </div>
            <div class="modal-footer border-top py-3">
                <a href="#" id="modalReplyBtn" class="btn btn-success rounded-pill px-4 fw-semibold" style="font-size: 0.85rem;"><i class="bi bi-reply-fill me-1"></i> Reply via Email</a>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="font-size: 0.85rem;">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('inquiryDetailModal');
    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.btn-view-inquiry').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const subject = this.getAttribute('data-subject');
            const message = this.getAttribute('data-message');
            const ip = this.getAttribute('data-ip');
            const date = this.getAttribute('data-date');
            const status = this.getAttribute('data-status');

            document.getElementById('modalSenderName').textContent = name;
            document.getElementById('modalDate').textContent = date;
            
            const emailLink = document.getElementById('modalEmailLink');
            emailLink.textContent = email;
            emailLink.href = 'mailto:' + email;

            const phoneLink = document.getElementById('modalPhoneLink');
            phoneLink.textContent = phone;
            phoneLink.href = phone !== 'N/A' ? 'tel:' + phone : '#';

            document.getElementById('modalSubject').textContent = subject;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalIp').textContent = ip;

            const replyBtn = document.getElementById('modalReplyBtn');
            replyBtn.href = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject);

            // If unread, mark as read via AJAX
            if (status === 'unread') {
                fetch(`/admin/contact-inquiries/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(res => res.json()).then(data => {
                    const row = document.getElementById('inquiryRow_' + id);
                    if (row) row.classList.remove('table-light', 'fw-semibold');
                    const badge = document.getElementById('badge_' + id);
                    if (badge) {
                        badge.className = 'badge bg-light text-muted border px-2 py-1 rounded-pill';
                        badge.textContent = 'Read';
                    }
                    btn.setAttribute('data-status', 'read');
                }).catch(err => console.error(err));
            }

            modal.show();
        });
    });
});
</script>
@endpush
