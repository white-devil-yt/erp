<header class="topbar">
    <button class="btn btn-link text-dark toggle-sidebar d-lg-none" id="sidebarToggle">
        <i class="bi bi-list fs-4"></i>
    </button>
    <div class="topbar-title">
        <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
        <span class="text-muted small" id="currentDate"></span>
    </div>
    <div class="ms-auto d-flex align-items-center gap-3">
        <div class="topbar-search d-none d-md-block">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Quick search..." id="quickSearch">
        </div>
        <div class="dropdown">
            <button class="btn btn-light rounded-circle p-2 position-relative" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><h6 class="dropdown-header">Notifications</h6></li>
                <li><a class="dropdown-item small" href="{{ route('stock.index') }}"><i class="bi bi-exclamation-triangle text-warning me-2"></i>Low stock items</a></li>
                <li><a class="dropdown-item small" href="{{ route('payslips.index') }}"><i class="bi bi-cash-stack text-success me-2"></i>Payslips pending</a></li>
                <li><a class="dropdown-item small" href="{{ route('sales.index') }}"><i class="bi bi-cart text-info me-2"></i>Unpaid invoices</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-light rounded-circle p-2" data-bs-toggle="dropdown">
                <div class="avatar avatar-sm bg-primary text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <div class="px-3 py-2">
                        <strong class="d-block">{{ auth()->user()->name }}</strong>
                        <span class="small text-muted">{{ auth()->user()->email }}</span>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>