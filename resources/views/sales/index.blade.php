@extends('layouts.app')

@section('title', 'Sales & Invoices')
@section('page-title', 'Sales & Invoices')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cart-check me-2 text-primary"></i>Sales & Invoices</h4>
    <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Invoice</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Invoice number...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Payment Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($sales->isEmpty())
            <div class="empty-state"><i class="bi bi-cart"></i><p class="mb-0">No invoices found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Due</th>
                            <th>Payment</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="fw-semibold text-decoration-none">{{ $sale->invoice_number }}</a>
                                </td>
                                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td class="small">{{ $sale->invoice_date->format('d M Y') }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($sale->total, 2) }}</td>
                                <td class="text-end {{ $sale->balance_due > 0 ? 'text-danger' : 'text-muted' }}">₹{{ number_format($sale->balance_due, 2) }}</td>
                                <td>
                                    @if ($sale->payment_status === 'paid')
                                        <span class="badge bg-soft-success">Paid</span>
                                    @elseif ($sale->payment_status === 'partial')
                                        <span class="badge bg-soft-warning">Partial</span>
                                    @else
                                        <span class="badge bg-soft-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('sales.print', $sale) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" title="Print"><i class="bi bi-printer"></i></a>
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this invoice? Stock will be restored.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $sales->links() }}</div>
        @endif
    </div>
</div>
@endsection