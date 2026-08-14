@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-purple">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Monthly Sales</span>
                <div class="stat-icon"><i class="bi bi-cart-check"></i></div>
            </div>
            <div class="stat-value">₹{{ number_format($stats['monthly_sales']) }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-green">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Monthly Purchases</span>
                <div class="stat-icon"><i class="bi bi-bag-plus"></i></div>
            </div>
            <div class="stat-value">₹{{ number_format($stats['monthly_purchases']) }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-blue">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Payroll (This Month)</span>
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
            </div>
            <div class="stat-value">₹{{ number_format($stats['monthly_payroll']) }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-orange">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Unpaid Invoices</span>
                <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
            </div>
            <div class="stat-value">₹{{ number_format($stats['unpaid_invoices']) }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-purple">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Products</span>
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            </div>
            <div class="stat-value">{{ $stats['total_products'] }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card stat-green">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-label">Employees</span>
                <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
            </div>
            <div class="stat-value">{{ $stats['total_employees'] }}</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Sales vs Purchases (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-pie-chart me-2 text-primary"></i>Sales by Category</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="categoryChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock Alerts</h6>
                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if ($lowStockProducts->isEmpty())
                    <div class="empty-state"><i class="bi bi-check-circle"></i>All products are well stocked</div>
                @else
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Stock Left</th>
                                <th>Alert Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockProducts as $product)
                                <tr>
                                    <td>
                                        <strong class="d-block">{{ $product->name }}</strong>
                                        <span class="small text-muted">{{ $product->sku }}</span>
                                    </td>
                                    <td><span class="badge bg-soft-danger">{{ $product->stock_quantity }} {{ $product->unit }}</span></td>
                                    <td class="text-muted small">{{ $product->low_stock_alert }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-cart3 me-2 text-primary"></i>Recent Invoices</h6>
            </div>
            <div class="card-body p-0">
                @if ($recentSales->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No sales yet</div>
                @else
                    <table class="table mb-0">
                        <tbody>
                            @foreach ($recentSales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="fw-semibold text-decoration-none">{{ $sale->invoice_number }}</a>
                                        <span class="d-block small text-muted">{{ $sale->customer->name ?? 'Walk-in' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>₹{{ number_format($sale->total, 2) }}</strong>
                                        <span class="d-block">
                                            @if ($sale->payment_status === 'paid')
                                                <span class="badge bg-soft-success">Paid</span>
                                            @elseif ($sale->payment_status === 'partial')
                                                <span class="badge bg-soft-warning">Partial</span>
                                            @else
                                                <span class="badge bg-soft-danger">Unpaid</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Top Salaries ({{ \Carbon\Carbon::now()->format('M Y') }})</h6>
                <span class="badge bg-soft-warning">{{ $pendingPayslips }} pending</span>
            </div>
            <div class="card-body p-0">
                @if ($recentPayslips->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No payslips yet</div>
                @else
                    <table class="table mb-0">
                        <tbody>
                            @foreach ($recentPayslips as $payslip)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-primary text-white me-2">{{ strtoupper(substr($payslip->employee->name, 0, 1)) }}</div>
                                            <div>
                                                <strong class="d-block">{{ $payslip->employee->name }}</strong>
                                                <span class="small text-muted">{{ $payslip->employee->position }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <strong>₹{{ number_format($payslip->net_salary, 2) }}</strong>
                                        <span class="d-block">
                                            @if ($payslip->status === 'paid')
                                                <span class="badge bg-soft-success">Paid</span>
                                            @else
                                                <span class="badge bg-soft-warning">Pending</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const salesData = @json($last6Months);
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: salesData.map(d => d.label),
        datasets: [
            {
                label: 'Sales',
                data: salesData.map(d => d.sales),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.12)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#6366f1'
            },
            {
                label: 'Purchases',
                data: salesData.map(d => d.purchases),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#10b981'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { usePointStyle: true } } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => '₹' + v.toLocaleString() }
            }
        }
    }
});

const catData = @json($categorySales);
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catData.map(d => d.name),
        datasets: [{
            data: catData.map(d => d.total),
            backgroundColor: ['#6366f1', '#10b981', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } } }
    }
});
</script>
@endpush