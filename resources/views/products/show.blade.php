@extends('layouts.app')

@section('title', $product->name)
@section('page-title', $product->name)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-box-seam me-2 text-primary"></i>Product Details</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-light"><i class="bi bi-database me-1"></i>Stock</a>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold">{{ $product->name }}</h5>
                <p class="text-muted small mb-3">{{ $product->sku }} • {{ $product->unit }}</p>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Category</span>
                    <strong>{{ $product->category->name ?? '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Purchase Price</span>
                    <strong>₹{{ number_format($product->purchase_price, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Sale Price</span>
                    <strong class="text-success">₹{{ number_format($product->sale_price, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Current Stock</span>
                    @if ($product->isLowStock())
                        <strong class="text-danger">{{ $product->stock_quantity }} {{ $product->unit }}</strong>
                    @else
                        <strong>{{ $product->stock_quantity }} {{ $product->unit }}</strong>
                    @endif
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Alert Level</span>
                    <strong>{{ $product->low_stock_alert }} {{ $product->unit }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Status</span>
                    @if ($product->is_active)
                        <span class="badge bg-soft-success">Active</span>
                    @else
                        <span class="badge bg-soft-secondary">Inactive</span>
                    @endif
                </div>
                @if ($product->description)
                    <div class="mt-3 p-3 bg-light rounded-3 small">
                        <strong class="d-block mb-1 text-muted text-uppercase" style="font-size:0.7rem">Description</strong>
                        {{ $product->description }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Recent Stock Movements</h6>
            </div>
            <div class="card-body p-0">
                @if ($movements->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No stock movements recorded</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="text-center">Qty</th>
                                    <th>Reference</th>
                                    <th>Notes</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movements as $movement)
                                    <tr>
                                        <td>
                                            @if ($movement->type === 'in')
                                                <span class="badge bg-soft-success">Stock In</span>
                                            @elseif ($movement->type === 'out')
                                                <span class="badge bg-soft-danger">Stock Out</span>
                                            @else
                                                <span class="badge bg-soft-warning">Adjustment</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold">{{ $movement->quantity }}</td>
                                        <td class="small text-muted">{{ str_replace('App\\Models\\', '', $movement->reference_type) }} #{{ $movement->reference_id ?? '—' }}</td>
                                        <td class="small">{{ $movement->notes }}</td>
                                        <td class="small text-muted">{{ $movement->created_at->format('d M Y, h:i A') }}</td>
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