@extends('layouts.app')

@section('title', 'Purchases')
@section('page-title', 'Purchases')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-bag-plus me-2 text-primary"></i>Purchases</h4>
    <a href="{{ route('purchases.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Purchase</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Purchase number...">
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
        @if ($purchases->isEmpty())
            <div class="empty-state"><i class="bi bi-bag"></i><p class="mb-0">No purchases found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Purchase #</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Due</th>
                            <th>Payment</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $purchase)
                            <tr>
                                <td>
                                    <a href="{{ route('purchases.show', $purchase) }}" class="fw-semibold text-decoration-none">{{ $purchase->purchase_number }}</a>
                                </td>
                                <td>{{ $purchase->supplier->name ?? '—' }}</td>
                                <td class="small">{{ $purchase->purchase_date->format('d M Y') }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($purchase->total, 2) }}</td>
                                <td class="text-end {{ $purchase->balance_due > 0 ? 'text-danger' : 'text-muted' }}">₹{{ number_format($purchase->balance_due, 2) }}</td>
                                <td>
                                    @if ($purchase->payment_status === 'paid')
                                        <span class="badge bg-soft-success">Paid</span>
                                    @elseif ($purchase->payment_status === 'partial')
                                        <span class="badge bg-soft-warning">Partial</span>
                                    @else
                                        <span class="badge bg-soft-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this purchase? Stock will be deducted.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $purchases->links() }}</div>
        @endif
    </div>
</div>
@endsection