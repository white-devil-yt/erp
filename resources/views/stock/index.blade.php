@extends('layouts.app')

@section('title', 'Stock Management')
@section('page-title', 'Stock Management')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-database me-2 text-primary"></i>Stock Management</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or SKU...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="low" {{ request('status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-boxes me-2 text-primary"></i>Current Stock Levels</h6>
            </div>
            <div class="card-body p-0">
                @if ($products->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No products found</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">In Stock</th>
                                    <th class="text-center">Alert</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td>
                                            <strong class="d-block">{{ $product->name }}</strong>
                                            <span class="small text-muted">{{ $product->sku }}</span>
                                        </td>
                                        <td class="text-center fw-semibold">{{ $product->stock_quantity }} {{ $product->unit }}</td>
                                        <td class="text-center text-muted">{{ $product->low_stock_alert }}</td>
                                        <td>
                                            @if ($product->stock_quantity <= 0)
                                                <span class="badge bg-soft-danger">Out of stock</span>
                                            @elseif ($product->isLowStock())
                                                <span class="badge bg-soft-warning">Low</span>
                                            @else
                                                <span class="badge bg-soft-success">OK</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#adjustModal"
                                                    data-product="{{ $product->id }}"
                                                    data-name="{{ $product->name }}"
                                                    data-stock="{{ $product->stock_quantity }}"
                                                    data-unit="{{ $product->unit }}">
                                                <i class="bi bi-sliders"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Movements</h6>
            </div>
            <div class="card-body p-0">
                @if ($recentMovements->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No movements yet</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($recentMovements as $movement)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="d-block small">{{ $movement->product->name ?? 'Product' }}</strong>
                                    <span class="small text-muted">{{ $movement->notes }}</span>
                                    <span class="d-block small text-muted">{{ $movement->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-end">
                                    @if ($movement->type === 'in')
                                        <span class="badge bg-soft-success">+{{ $movement->quantity }}</span>
                                    @elseif ($movement->type === 'out')
                                        <span class="badge bg-soft-danger">-{{ $movement->quantity }}</span>
                                    @else
                                        <span class="badge bg-soft-warning">{{ $movement->quantity }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('stock.adjust') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-sliders me-2 text-primary"></i>Adjust Stock</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="product_id" id="adjustProductId">
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" id="adjustProductName" class="form-control" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Operation</label>
                    <select name="type" id="adjustType" class="form-select">
                        <option value="in">Add Stock (Stock In)</option>
                        <option value="out">Remove Stock (Stock Out)</option>
                        <option value="adjustment">Set to Exact Quantity</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" step="0.01" min="0.01" name="quantity" class="form-control" required>
                    <div class="form-text">Current stock: <span id="adjustCurrentStock"></span></div>
                </div>
                <div>
                    <label class="form-label">Notes (optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="e.g. Damaged goods, restock, audit">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-bs-target="#adjustModal"]').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('adjustProductId').value = this.dataset.product;
        document.getElementById('adjustProductName').value = this.dataset.name;
        document.getElementById('adjustCurrentStock').textContent = this.dataset.stock + ' ' + this.dataset.unit;
    });
});
</script>
@endpush