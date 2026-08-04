<div class="col-lg-3">
    <div class="bg-white p-3 rounded-4 shadow-sm border customer-sidebar" style="border-color: var(--border-color) !important;">
        <div class="text-center py-3 border-bottom mb-3">
            <div class="avatar bg-success text-white rounded-circle mx-auto d-flex align-items-center justify-content-center font-heading fs-3 fw-bold" style="width: 70px; height: 70px;">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <h5 class="fw-bold font-heading mt-2 mb-0">{{ Auth::user()->name }}</h5>
            <span class="text-muted" style="font-size: 0.8rem;">{{ Auth::user()->roles->first()?->display_name ?? 'Retail Customer' }}</span>
        </div>

        <div class="list-group list-group-flush border-0">
            <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action py-3 border-0 rounded-3 {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard Overview
            </a>
            <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action py-3 border-0 rounded-3 {{ request()->routeIs('customer.orders', 'customer.orders.show') ? 'active' : '' }}">
                <i class="bi bi-bag me-2"></i> My Order History
            </a>
            <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action py-3 border-0 rounded-3 {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                <i class="bi bi-person-gear me-2"></i> Profile Settings
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();" class="list-group-item list-group-item-action py-3 border-0 rounded-3 text-danger">
                <i class="bi bi-box-arrow-right me-2"></i> Logout Account
            </a>
        </div>
        
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</div>
