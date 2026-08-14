@extends('layouts.app')

@section('title', $purchase->purchase_number)
@section('page-title', $purchase->purchase_number)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-bag-plus me-2 text-primary"></i>Purchase Details</h4>
    <div class="d-flex gap-2">
        @if ($purchase->balance_due > 0)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-cash-coin me-1"></i>Record Payment
            </button>
        @endif
        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Delete this purchase? Stock will be deducted.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchase->items as $item)
                                <tr>
                                    <td>
                                        <strong class="d-block">{{ $item->product->name }}</strong>
                                        <span class="small text-muted">{{ $item->product->sku }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->product->unit }}</td>
                                    <td class="text-end">₹{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="text-end fw-semibold">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    <div class="ms-auto" style="max-width:300px">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Subtotal</span>
                            <strong>₹{{ number_format($purchase->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Tax ({{ $purchase->tax_rate }}%)</span>
                            <strong>₹{{ number_format($purchase->tax_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Discount</span>
                            <strong class="text-danger">-₹{{ number_format($purchase->discount, 2) }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total</span>
                            <strong class="fs-5 text-primary">₹{{ number_format($purchase->total, 2) }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Amount Paid</span>
                            <strong class="text-success">₹{{ number_format($purchase->amount_paid, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Balance Due</span>
                            <strong class="{{ $purchase->balance_due > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($purchase->balance_due, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Purchase Info</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Purchase #</span>
                    <strong>{{ $purchase->purchase_number }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Date</span>
                    <span>{{ $purchase->purchase_date->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Payment Method</span>
                    <span class="text-capitalize">{{ $purchase->payment_method ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Payment Status</span>
                    @if ($purchase->payment_status === 'paid')
                        <span class="badge bg-soft-success">Paid</span>
                    @elseif ($purchase->payment_status === 'partial')
                        <span class="badge bg-soft-warning">Partial</span>
                    @else
                        <span class="badge bg-soft-danger">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-truck me-2 text-primary"></i>Supplier</h6></div>
            <div class="card-body">
                @if ($purchase->supplier)
                    <h6 class="fw-bold mb-1">{{ $purchase->supplier->name }}</h6>
                    <p class="small text-muted mb-1">{{ $purchase->supplier->company }}</p>
                    <p class="small mb-0">
                        <i class="bi bi-envelope me-1"></i>{{ $purchase->supplier->email ?? '—' }}<br>
                        <i class="bi bi-telephone me-1"></i>{{ $purchase->supplier->phone ?? '—' }}<br>
                        <i class="bi bi-geo-alt me-1"></i>{{ $purchase->supplier->address ?? '—' }}
                    </p>
                @else
                    <p class="text-muted mb-0">No supplier</p>
                @endif
            </div>
        </div>
    </div>
</div>

@if ($purchase->balance_due > 0)
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('purchases.payment', $purchase) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cash-coin me-2 text-success"></i>Record Payment</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border small mb-3">
                    Balance due: <strong>₹{{ number_format($purchase->balance_due, 2) }}</strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" max="{{ $purchase->balance_due }}" name="amount" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash">Cash</option>
                        <option value="bank transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Record Payment</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection