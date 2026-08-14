<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-boxes"></i></div>
        <div class="brand-text">
            <span class="brand-name">{{ setting('company_name', config('app.name')) }}</span>
            <span class="brand-sub">{{ setting('company_tagline', 'Business Suite') }}</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-label">Main</p>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>

        <p class="nav-label">Inventory</p>
        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products*') ? 'active' : '' }}">
            <i class="bi bi-box-seam"></i><span>Products</span>
        </a>
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i><span>Categories</span>
        </a>
        <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock*') ? 'active' : '' }}">
            <i class="bi bi-database"></i><span>Stock</span>
        </a>

        <p class="nav-label">CRM</p>
        <a href="{{ route('leads.index') }}" class="nav-link {{ request()->routeIs('leads*') ? 'active' : '' }}">
            <i class="bi bi-person-lines-fill"></i><span>Leads</span>
        </a>

        <p class="nav-label">Sales & Purchases</p>
        <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales*') ? 'active' : '' }}">
            <i class="bi bi-cart-check"></i><span>Sales / Invoices</span>
        </a>
        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
            <i class="bi bi-people"></i><span>Customers</span>
        </a>
        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases*') ? 'active' : '' }}">
            <i class="bi bi-bag-plus"></i><span>Purchases</span>
        </a>
        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i><span>Suppliers</span>
        </a>

        <p class="nav-label">Payroll</p>
        <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees*') ? 'active' : '' }}">
            <i class="bi bi-person-badge"></i><span>Employees</span>
        </a>
        <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments*') ? 'active' : '' }}">
            <i class="bi bi-diagram-3"></i><span>Departments</span>
        </a>
        <a href="{{ route('attendance.index') }}" class="nav-link {{ request()->routeIs('attendance*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i><span>Attendance</span>
        </a>
        <a href="{{ route('payslips.index') }}" class="nav-link {{ request()->routeIs('payslips*') ? 'active' : '' }}">
            <i class="bi bi-cash-coin"></i><span>Payslips</span>
        </a>

        <p class="nav-label">Reports</p>
        <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
            <i class="bi bi-graph-up"></i><span>Sales Report</span>
        </a>
        <a href="{{ route('reports.stock') }}" class="nav-link {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
            <i class="bi bi-clipboard-data"></i><span>Stock Report</span>
        </a>
        <a href="{{ route('reports.payroll') }}" class="nav-link {{ request()->routeIs('reports.payroll') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i><span>Payroll Report</span>
        </a>

        <p class="nav-label">Accounting</p>
        <a href="{{ route('accounting.dashboard') }}" class="nav-link {{ request()->routeIs('accounting.dashboard') ? 'active' : '' }}">
            <i class="bi bi-calculator"></i><span>Accounting</span>
        </a>
        <a href="{{ route('accounting.accounts') }}" class="nav-link {{ request()->routeIs('accounting.accounts') ? 'active' : '' }}">
            <i class="bi bi-journal-richtext"></i><span>Chart of Accounts</span>
        </a>
        <a href="{{ route('accounting.journal') }}" class="nav-link {{ request()->routeIs('accounting.journal*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i><span>Journal Entries</span>
        </a>
        <a href="{{ route('accounting.trial-balance') }}" class="nav-link {{ request()->routeIs('accounting.trial-balance') ? 'active' : '' }}">
            <i class="bi bi-columns-gap"></i><span>Trial Balance</span>
        </a>
        <a href="{{ route('accounting.income-statement') }}" class="nav-link {{ request()->routeIs('accounting.income-statement') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i><span>Income Statement</span>
        </a>
        <a href="{{ route('accounting.balance-sheet') }}" class="nav-link {{ request()->routeIs('accounting.balance-sheet') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-bar-graph"></i><span>Balance Sheet</span>
        </a>

        <p class="nav-label">Settings</p>
        <a href="{{ route('billing.settings') }}" class="nav-link {{ request()->routeIs('billing*') ? 'active' : '' }}">
            <i class="bi bi-sliders"></i><span>Billing & Invoice</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm bg-primary text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="ms-2 user-meta">
                <strong class="d-block text-white small">{{ auth()->user()->name }}</strong>
                <span class="small text-white-50">{{ ucfirst(auth()->user()->role) }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="btn btn-sm btn-link text-white-50 p-0" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>