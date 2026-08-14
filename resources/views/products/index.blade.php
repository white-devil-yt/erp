@extends('layouts.app')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-box-seam me-2 text-primary"></i>Products</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('stock.index') }}" class="btn btn-light"><i class="bi bi-database me-1"></i>Stock</a>
        <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Product</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or SKU...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock"
                           {{ request('low_stock') ? 'checked' : '' }}>
                    <label class="form-check-label" for="lowStock">Low stock only</label>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($products->isEmpty())
            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                <p class="mb-0">No products found.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Purchase Price</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('products.show', $product) }}" class="fw-semibold text-decoration-none">{{ $product->name }}</a>
                                    <span class="d-block small text-muted">{{ $product->sku }} • {{ $product->unit }}</span>
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td class="text-end">₹{{ number_format($product->purchase_price, 2) }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($product->sale_price, 2) }}</td>
                                <td class="text-center">
                                    @if ($product->isLowStock())
                                        <span class="badge bg-soft-danger">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                                    @else
                                        <span class="badge bg-soft-success">{{ $product->stock_quantity }} {{ $product->unit }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->is_active)
                                        <span class="badge bg-soft-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection