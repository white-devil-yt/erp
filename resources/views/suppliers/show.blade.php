@extends('layouts.app')

@section('title', $supplier->name)
@section('page-title', $supplier->name)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-truck me-2 text-primary"></i>Supplier Profile</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('purchases.create') }}" class="btn btn-success"><i class="bi bi-bag-plus me-1"></i>New Purchase</a>
        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="avatar mx-auto mb-3 bg-success text-white" style="width:64px;height:64px;font-size:1.6rem">
                    {{ strtoupper(substr($supplier->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $supplier->name }}</h5>
                <p class="text-muted small mb-3">{{ $supplier->company ?? 'Individual' }}</p>
                @if ($supplier->is_active)
                    <span class="badge bg-soft-success">Active</span>
                @else
                    <span class="badge bg-soft-secondary">Inactive</span>
                @endif
            </div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-envelope me-1"></i>Email</span>
                    <span class="small">{{ $supplier->email ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-telephone me-1"></i>Phone</span>
                    <span class="small">{{ $supplier->phone ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-receipt me-1"></i>GST</span>
                    <span class="small">{{ $supplier->gst_number ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-geo-alt me-1"></i>Address</span>
                    <span class="small text-end">{{ $supplier->address ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted"><i class="bi bi-bag me-1"></i>Total Purchases</span>
                    <span class="fw-semibold">{{ $supplier->purchases->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bag-plus me-2 text-primary"></i>Purchase History</h6>
            </div>
            <div class="card-body p-0">
                @if ($purchases->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No purchases from this supplier yet</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Purchase</th>
                                    <th>Date</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchases.show', $purchase) }}" class="fw-semibold text-decoration-none">{{ $purchase->purchase_number }}</a>
                                        </td>
                                        <td class="small">{{ $purchase->purchase_date->format('d M Y') }}</td>
                                        <td class="text-end">₹{{ number_format($purchase->total, 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format($purchase->amount_paid, 2) }}</td>
                                        <td class="text-end {{ $purchase->balance_due > 0 ? 'text-danger' : '' }}">₹{{ number_format($purchase->balance_due, 2) }}</td>
                                        <td>
                                            @if ($purchase->payment_status === 'paid')
                                                <span class="badge bg-soft-success">Paid</span>
                                            @elseif ($purchase->payment_status === 'partial')
                                                <span class="badge bg-soft-warning">Partial</span>
                                            @else
                                                <span class="badge bg-soft-danger">Unpaid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection