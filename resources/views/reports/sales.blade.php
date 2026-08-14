@extends('layouts.app')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-graph-up me-2 text-primary"></i>Sales Report</h4>
    <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">From</label>
                <input type="date" name="from" class="form-control" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">To</label>
                <input type="date" name="to" class="form-control" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-2 col-6"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold">{{ $summary['count'] }}</div><div class="small text-muted">Invoices</div>
    </div></div></div>
    <div class="col-md-2 col-6"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold">₹{{ number_format($summary['subtotal']) }}</div><div class="small text-muted">Subtotal</div>
    </div></div></div>
    <div class="col-md-2 col-6"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold">₹{{ number_format($summary['tax']) }}</div><div class="small text-muted">Tax Collected</div>
    </div></div></div>
    <div class="col-md-2 col-6"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-danger">-₹{{ number_format($summary['discount']) }}</div><div class="small text-muted">Discounts</div>
    </div></div></div>
    <div class="col-md-2 col-6"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-primary">₹{{ number_format($summary['total']) }}</div><div class="small text-muted">Total Sales</div>
    </div></div></div>
    <div class="col-md-2 col-6"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-danger">₹{{ number_format($summary['due']) }}</div><div class="small text-muted">Outstanding</div>
    </div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>Daily Sales Trend</h6></div>
            <div class="card-body"><canvas id="salesTrend" height="100"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-trophy me-2 text-primary"></i>Top Customers</h6></div>
            <div class="card-body p-0">
                @if ($topCustomers->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No data</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($topCustomers as $customer)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-semibold small">{{ $customer['name'] }}</span>
                                <span class="badge bg-soft-primary">₹{{ number_format($customer['total'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Invoice Details ({{ $from->format('d M Y') }} — {{ $to->format('d M Y') }})</h6>
    </div>
    <div class="card-body p-0">
        @if ($sales->isEmpty())
            <div class="empty-state"><i class="bi bi-inbox"></i>No sales in this period</div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th><th>Customer</th><th>Date</th>
                            <th class="text-end">Subtotal</th><th class="text-end">Tax</th>
                            <th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}" class="fw-semibold text-decoration-none">{{ $sale->invoice_number }}</a></td>
                                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td class="small">{{ $sale->invoice_date->format('d M Y') }}</td>
                                <td class="text-end">₹{{ number_format($sale->subtotal, 2) }}</td>
                                <td class="text-end">₹{{ number_format($sale->tax_amount, 2) }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($sale->total, 2) }}</td>
                                <td class="text-end text-success">₹{{ number_format($sale->amount_paid, 2) }}</td>
                                <td class="text-end {{ $sale->balance_due > 0 ? 'text-danger' : '' }}">₹{{ number_format($sale->balance_due, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="3">TOTALS</td>
                            <td class="text-end">₹{{ number_format($summary['subtotal'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($summary['tax'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($summary['total'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($summary['paid'], 2) }}</td>
                            <td class="text-end">₹{{ number_format($summary['due'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const daily = @json($daily);
new Chart(document.getElementById('salesTrend'), {
    type: 'bar',
    data: {
        labels: Object.keys(daily),
        datasets: [{
            label: 'Daily Sales (₹)',
            data: Object.values(daily),
            backgroundColor: 'rgba(99, 102, 241, 0.7)',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString() } }
        }
    }
});
</script>
@endpush