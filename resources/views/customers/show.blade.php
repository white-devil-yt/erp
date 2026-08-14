@extends('layouts.app')

@section('title', $customer->name)
@section('page-title', $customer->name)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person me-2 text-primary"></i>Customer Profile</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-success"><i class="bi bi-cart-plus me-1"></i>New Invoice</a>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="avatar mx-auto mb-3 bg-primary text-white" style="width:64px;height:64px;font-size:1.6rem">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                <p class="text-muted small mb-3">{{ $customer->company ?? 'Individual' }}</p>
                @if ($customer->is_active)
                    <span class="badge bg-soft-success">Active</span>
                @else
                    <span class="badge bg-soft-secondary">Inactive</span>
                @endif
            </div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-envelope me-1"></i>Email</span>
                    <span class="small">{{ $customer->email ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-telephone me-1"></i>Phone</span>
                    <span class="small">{{ $customer->phone ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-receipt me-1"></i>GST</span>
                    <span class="small">{{ $customer->gst_number ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-geo-alt me-1"></i>Address</span>
                    <span class="small text-end">{{ $customer->address ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted"><i class="bi bi-cart me-1"></i>Total Invoices</span>
                    <span class="fw-semibold">{{ $customer->sales->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Invoice History</h6>
            </div>
            <div class="card-body p-0">
                @if ($sales->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No invoices for this customer yet</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales.show', $sale) }}" class="fw-semibold text-decoration-none">{{ $sale->invoice_number }}</a>
                                        </td>
                                        <td class="small">{{ $sale->invoice_date->format('d M Y') }}</td>
                                        <td class="text-end">₹{{ number_format($sale->total, 2) }}</td>
                                        <td class="text-end text-success">₹{{ number_format($sale->amount_paid, 2) }}</td>
                                        <td class="text-end {{ $sale->balance_due > 0 ? 'text-danger' : '' }}">₹{{ number_format($sale->balance_due, 2) }}</td>
                                        <td>
                                            @if ($sale->payment_status === 'paid')
                                                <span class="badge bg-soft-success">Paid</span>
                                            @elseif ($sale->payment_status === 'partial')
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