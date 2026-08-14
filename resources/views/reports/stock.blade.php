@extends('layouts.app')

@section('title', 'Stock Report')
@section('page-title', 'Stock Report')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-clipboard-data me-2 text-primary"></i>Stock Report</h4>
    <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold">{{ $products->count() }}</div><div class="small text-muted">Total Products</div>
    </div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-warning">{{ $lowStock->count() }}</div><div class="small text-muted">Low Stock Items</div>
    </div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-primary">₹{{ number_format($stockValue) }}</div><div class="small text-muted">Stock Value (Cost)</div>
    </div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-success">₹{{ number_format($saleValue) }}</div><div class="small text-muted">Stock Value (Sale)</div>
    </div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-boxes me-2 text-primary"></i>Inventory Valuation</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Cost/Unit</th>
                                <th class="text-end">Stock Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <strong class="d-block">{{ $product->name }}</strong>
                                        <span class="small text-muted">{{ $product->sku }}</span>
                                    </td>
                                    <td>{{ $product->category->name ?? '—' }}</td>
                                    <td class="text-center">{{ $product->stock_quantity }} {{ $product->unit }}</td>
                                    <td class="text-end">₹{{ number_format($product->purchase_price, 2) }}</td>
                                    <td class="text-end fw-semibold">₹{{ number_format($product->stock_quantity * $product->purchase_price, 2) }}</td>
                                    <td>
                                        @if ($product->stock_quantity <= 0)
                                            <span class="badge bg-soft-danger">Out</span>
                                        @elseif ($product->isLowStock())
                                            <span class="badge bg-soft-warning">Low</span>
                                        @else
                                            <span class="badge bg-soft-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4">TOTAL STOCK VALUE</td>
                                <td class="text-end">₹{{ number_format($stockValue, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Low Stock Alert List</h6>
            </div>
            <div class="card-body p-0">
                @if ($lowStock->isEmpty())
                    <div class="empty-state"><i class="bi bi-check-circle"></i>All products are well stocked</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($lowStock as $product)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block small">{{ $product->name }}</strong>
                                    <span class="small text-muted">Alert at {{ $product->low_stock_alert }} {{ $product->unit }}</span>
                                </div>
                                <span class="badge bg-soft-danger">{{ $product->stock_quantity }} {{ $product->unit }} left</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Recent Movements</h6>
            </div>
            <div class="card-body p-0">
                @if ($movements->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No movements</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th class="text-center">Qty</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movements as $movement)
                                    <tr>
                                        <td class="small">{{ $movement->product->name ?? '—' }}</td>
                                        <td>
                                            @if ($movement->type === 'in')
                                                <span class="badge bg-soft-success">In</span>
                                            @elseif ($movement->type === 'out')
                                                <span class="badge bg-soft-danger">Out</span>
                                            @else
                                                <span class="badge bg-soft-warning">Adj</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $movement->quantity }}</td>
                                        <td class="small text-muted">{{ $movement->created_at->format('d M Y') }}</td>
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