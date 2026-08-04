{{--
  Admin Topbar Component
  Usage: <x-admin.topbar />
--}}
<header class="admin-topbar" id="adminTopbar">

    {{-- Mobile hamburger --}}
    <button class="topbar-hamburger" id="topbarHamburger" aria-label="Open Sidebar">
        <i class="bi bi-list"></i>
    </button>

    {{-- Desktop sidebar collapse toggle --}}
    <button class="sidebar-toggle-btn d-none d-lg-flex" id="sidebarToggleBtn" title="Collapse Sidebar">
        <i class="bi bi-chevron-left"></i>
    </button>

    {{-- Page breadcrumb / title --}}
    <div class="topbar-title">
        <h2 id="topbarPageTitle">Dashboard</h2>
        {{-- Breadcrumb injected per page via @section('breadcrumb') --}}
        @hasSection('breadcrumb')
            <nav aria-label="breadcrumb">
                @yield('breadcrumb')
            </nav>
        @endif
    </div>

    {{-- Search --}}
    <div class="topbar-search">
        <i class="bi bi-search topbar-search-icon"></i>
        <input type="text" placeholder="Search…" id="adminGlobalSearch" autocomplete="off">
    </div>

    {{-- Action Icons --}}
    <div class="topbar-actions">

        {{-- Notifications (placeholder) --}}
        <button class="topbar-action-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="topbar-badge"></span>
        </button>

        {{-- View Site --}}
        <a href="{{ route('home') }}" target="_blank" class="topbar-action-btn" title="View Site">
            <i class="bi bi-box-arrow-up-right"></i>
        </a>

    </div>

    {{-- User dropdown --}}
    <div style="position:relative;">
        <button class="topbar-user" id="topbarUserBtn" type="button">
            <div class="topbar-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="topbar-user-info">
                <span class="topbar-user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                <span class="topbar-user-role">Administrator</span>
            </div>
            <i class="bi bi-chevron-down topbar-chevron"></i>
        </button>

        {{-- Dropdown --}}
        <div class="topbar-dropdown d-none" id="topbarUserDropdown">
            <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                <i class="bi bi-gear"></i> Settings
            </a>
            <a href="{{ route('home') }}" target="_blank" class="dropdown-item">
                <i class="bi bi-box-arrow-up-right"></i> View Site
            </a>
            <div class="dropdown-divider"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger no-spinner w-100 border-0 bg-transparent text-start">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
        </div>
    </div>

</header>
