@extends('layouts.app')

@section('title', 'New Purchase')
@section('page-title', 'Create New Purchase')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bag-plus me-2 text-primary"></i>New Purchase Order</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select">
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }} {{ $supplier->company ? '— ' . $supplier->company : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', 18) }}" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Discount (₹)</label>
                            <input type="number" name="discount" class="form-control" value="0" min="0" step="0.01">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Items</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                            <i class="bi bi-plus-lg me-1"></i>Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th style="width:35%">Product</th>
                                    <th style="width:15%">Quantity</th>
                                    <th style="width:18%">Unit Cost (₹)</th>
                                    <th style="width:18%">Total (₹)</th>
                                    <th style="width:14%"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr class="item-row">
                                    <td>
                                        <select class="form-select product-select" name="items[0][product_id]" required>
                                            <option value="">Select product...</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-cost="{{ $product->purchase_price }}">
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control item-qty" min="0.01" step="0.01" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][unit_cost]" class="form-control item-cost" min="0" step="0.01" required>
                                    </td>
                                    <td class="item-total fw-semibold">₹0.00</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-item"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-end mt-3">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Subtotal</span>
                                <strong id="subtotalDisplay">₹0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Tax (18%)</span>
                                <strong id="taxDisplay">₹0.00</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold fs-5">Grand Total</span>
                                <strong class="fs-5 text-primary" id="grandTotalDisplay">₹0.00</strong>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select" required>
                                <option value="unpaid">Unpaid</option>
                                <option value="partial">Partial (50%)</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select">
                                <option value="">—</option>
                                <option value="cash">Cash</option>
                                <option value="bank transfer">Bank Transfer</option>
                                <option value="UPI">UPI</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional notes">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Record Purchase</button>
                        <a href="{{ route('purchases.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const products = @json($productOptions);
let rowIndex = 1;

function recalc() {
    let subtotal = 0;
    document.querySelectorAll('#itemsBody .item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
        const total = qty * cost;
        subtotal += total;
        row.querySelector('.item-total').textContent = '₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
    });
    const taxRate = parseFloat(document.querySelector('[name=tax_rate]').value) || 0;
    const discount = parseFloat(document.querySelector('[name=discount]').value) || 0;
    const tax = subtotal * taxRate / 100;
    const grand = subtotal + tax - discount;
    document.getElementById('subtotalDisplay').textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('taxDisplay').textContent = '₹' + tax.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('grandTotalDisplay').textContent = '₹' + Math.max(0, grand).toLocaleString('en-IN', {minimumFractionDigits: 2});
}

function bindRow(row) {
    const select = row.querySelector('.product-select');
    const costInput = row.querySelector('.item-cost');
    select.addEventListener('change', function () {
        const product = products.find(p => p.id == this.value);
        if (product) costInput.value = product.cost;
        recalc();
    });
    row.querySelectorAll('.item-qty, .item-cost').forEach(input => {
        input.addEventListener('input', recalc);
    });
    row.querySelector('.remove-item').addEventListener('click', function () {
        if (document.querySelectorAll('#itemsBody .item-row').length > 1) {
            row.remove();
            recalc();
        }
    });
}

document.getElementById('addItemBtn').addEventListener('click', function () {
    const tbody = document.getElementById('itemsBody');
    const template = tbody.querySelector('.item-row').cloneNode(true);
    template.querySelectorAll('[name]').forEach(input => {
        input.name = input.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
        if (input.classList.contains('product-select')) input.value = '';
        if (input.classList.contains('item-cost')) input.value = '';
        if (input.classList.contains('item-qty')) input.value = '1';
    });
    template.querySelector('.item-total').textContent = '₹0.00';
    rowIndex++;
    tbody.appendChild(template);
    bindRow(template);
});

document.querySelector('[name=tax_rate]').addEventListener('input', recalc);
document.querySelector('[name=discount]').addEventListener('input', recalc);
bindRow(document.querySelector('.item-row'));
recalc();

document.getElementById('purchaseForm').addEventListener('submit', function (e) {
    const rows = document.querySelectorAll('#itemsBody .item-row');
    let valid = rows.length > 0;
    rows.forEach(row => {
        if (!row.querySelector('.product-select').value) valid = false;
    });
    if (!valid) {
        e.preventDefault();
        alert('Please select a product for each item row.');
    }
});
</script>
@endpush