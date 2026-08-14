@extends('layouts.app')

@section('title', $sale->invoice_number)
@section('page-title', $sale->invoice_number)

@section('content')
<div class="page-header no-print">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Invoice Details</h4>
    <div class="d-flex gap-2">
        @if ($sale->balance_due > 0)
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-cash-coin me-1"></i>Record Payment
            </button>
        @endif
        <a href="{{ route('sales.print', $sale) }}" target="_blank" class="btn btn-light"><i class="bi bi-printer me-1"></i>Print</a>
        <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this invoice? Stock will be restored.');">
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
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td>
                                        <strong class="d-block">{{ $item->product->name }}</strong>
                                        <span class="small text-muted">{{ $item->product->sku }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->product->unit }}</td>
                                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
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
                            <strong>₹{{ number_format($sale->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Tax ({{ $sale->tax_rate }}%)</span>
                            <strong>₹{{ number_format($sale->tax_amount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Discount</span>
                            <strong class="text-danger">-₹{{ number_format($sale->discount, 2) }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Grand Total</span>
                            <strong class="fs-5 text-primary">₹{{ number_format($sale->total, 2) }}</strong>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Amount Paid</span>
                            <strong class="text-success">₹{{ number_format($sale->amount_paid, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Balance Due</span>
                            <strong class="{{ $sale->balance_due > 0 ? 'text-danger' : 'text-success' }}">₹{{ number_format($sale->balance_due, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Invoice Info</h6></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Invoice #</span>
                    <strong>{{ $sale->invoice_number }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Date</span>
                    <span>{{ $sale->invoice_date->format('d M Y') }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Due Date</span>
                    <span>{{ $sale->due_date?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Payment Method</span>
                    <span class="text-capitalize">{{ $sale->payment_method ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Payment Status</span>
                    @if ($sale->payment_status === 'paid')
                        <span class="badge bg-soft-success">Paid</span>
                    @elseif ($sale->payment_status === 'partial')
                        <span class="badge bg-soft-warning">Partial</span>
                    @else
                        <span class="badge bg-soft-danger">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-person me-2 text-primary"></i>Customer</h6></div>
            <div class="card-body">
                @if ($sale->customer)
                    <h6 class="fw-bold mb-1">{{ $sale->customer->name }}</h6>
                    <p class="small text-muted mb-1">{{ $sale->customer->company }}</p>
                    <p class="small mb-0">
                        <i class="bi bi-envelope me-1"></i>{{ $sale->customer->email ?? '—' }}<br>
                        <i class="bi bi-telephone me-1"></i>{{ $sale->customer->phone ?? '—' }}<br>
                        <i class="bi bi-geo-alt me-1"></i>{{ $sale->customer->address ?? '—' }}
                    </p>
                @else
                    <p class="text-muted mb-0">Walk-in customer</p>
                @endif
            </div>
        </div>

        @if ($sale->notes)
            <div class="card">
                <div class="card-header"><h6 class="mb-0"><i class="bi bi-sticky me-2 text-primary"></i>Notes</h6></div>
                <div class="card-body"><p class="small mb-0">{{ $sale->notes }}</p></div>
            </div>
        @endif
    </div>
</div>

<!-- Payment Modal -->
@if ($sale->balance_due > 0)
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('sales.payment', $sale) }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cash-coin me-2 text-success"></i>Record Payment</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light border small mb-3">
                    Balance due: <strong>₹{{ number_format($sale->balance_due, 2) }}</strong>
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0.01" max="{{ $sale->balance_due }}" name="amount" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash">Cash</option>
                        <option value="bank transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                        <option value="card">Card</option>
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