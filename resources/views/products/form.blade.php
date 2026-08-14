@extends('layouts.app')

@section('title', $product->exists ? 'Edit Product' : 'New Product')
@section('page-title', $product->exists ? 'Edit Product' : 'New Product')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>{{ $product->exists ? 'Edit Product' : 'Create Product' }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
                    @csrf
                    @if ($product->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                   value="{{ old('sku', $product->sku) }}" required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">No Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select">
                                @foreach (['pcs', 'box', 'kg', 'gram', 'liter', 'meter', 'ream', 'pack'] as $unit)
                                    <option value="{{ $unit }}" {{ old('unit', $product->unit) === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $product->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $product->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Purchase Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror"
                                   value="{{ old('purchase_price', $product->purchase_price) }}" required>
                            @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sale Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror"
                                   value="{{ old('sale_price', $product->sale_price) }}" required>
                            @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Low Stock Alert <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="low_stock_alert" class="form-control @error('low_stock_alert') is-invalid @enderror"
                                   value="{{ old('low_stock_alert', $product->low_stock_alert) }}" required>
                            @error('low_stock_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @if (!$product->exists)
                            <div class="col-md-6">
                                <label class="form-label">Opening Stock <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror"
                                       value="{{ old('stock_quantity', 0) }}" required>
                                @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="text-muted small"><i class="bi bi-info-circle me-1"></i>Opening stock is added as a stock-in movement.</div>
                            </div>
                        @else
                            <div class="col-md-6">
                                <label class="form-label">Current Stock</label>
                                <input type="text" class="form-control" value="{{ $product->stock_quantity }} {{ $product->unit }}" disabled>
                                <div class="form-text">Use the <a href="{{ route('stock.index') }}">Stock</a> page to adjust stock.</div>
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>{{ $product->exists ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection