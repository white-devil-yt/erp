@extends('layouts.app')

@section('title', 'New Invoice')
@section('page-title', 'Create New Invoice')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-cart-plus me-2 text-primary"></i>New Sale Invoice</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select">
                                <option value="">Walk-in Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->company ? '— ' . $customer->company : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Invoice Date <span class="text-danger">*</span></label>
                            <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', date('Y-m-d', strtotime('+15 days'))) }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" class="form-control" value="{{ old('tax_rate', setting('invoice_default_tax_rate', 18)) }}" min="0" max="100" step="0.01">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Items</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn">
                            <i class="bi bi-plus-lg me-1"></i>Add Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width:35%">Product</th>
                                    <th style="width:15%">Quantity</th>
                                    <th style="width:18%">Unit Price (₹)</th>
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
                                                <option value="{{ $product->id }}"
                                                        data-price="{{ $product->sale_price }}"
                                                        data-stock="{{ $product->stock_quantity }}"
                                                        data-unit="{{ $product->unit }}">
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]" class="form-control item-qty" min="0.01" step="0.01" value="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][unit_price]" class="form-control item-price" min="0" step="0.01" required>
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
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Discount (₹)</span>
                                <input type="number" name="discount" id="discountInput" class="form-control form-control-sm text-end" style="width:130px" value="0" min="0" step="0.01">
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
                                <option value="card">Card</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="Optional invoice notes">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Create Invoice</button>
                        <a href="{{ route('sales.index') }}" class="btn btn-light">Cancel</a>
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
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = qty * price;
        subtotal += total;
        row.querySelector('.item-total').textContent = '₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
    });
    const taxRate = parseFloat(document.querySelector('[name=tax_rate]').value) || 0;
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const tax = subtotal * taxRate / 100;
    const grand = subtotal + tax - discount;
    document.getElementById('subtotalDisplay').textContent = '₹' + subtotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('taxDisplay').textContent = '₹' + tax.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('grandTotalDisplay').textContent = '₹' + Math.max(0, grand).toLocaleString('en-IN', {minimumFractionDigits: 2});
}

function bindRow(row) {
    const select = row.querySelector('.product-select');
    const priceInput = row.querySelector('.item-price');
    select.addEventListener('change', function () {
        const product = products.find(p => p.id == this.value);
        if (product) {
            priceInput.value = product.price;
            priceInput.dataset.stock = product.stock;
            recalc();
        }
    });
    row.querySelectorAll('.item-qty, .item-price').forEach(input => {
        input.addEventListener('input', recalc);
    });
    row.querySelector('.item-qty').addEventListener('change', function () {
        const product = products.find(p => p.id == select.value);
        if (product && parseFloat(this.value) > product.stock) {
            alert('Only ' + product.stock + ' ' + product.unit + ' of ' + product.name + ' in stock.');
            this.value = product.stock;
            recalc();
        }
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
        if (input.classList.contains('item-price')) input.value = '';
        if (input.classList.contains('item-qty')) input.value = '1';
    });
    template.querySelector('.item-total').textContent = '₹0.00';
    rowIndex++;
    tbody.appendChild(template);
    bindRow(template);
});

document.querySelector('[name=tax_rate]').addEventListener('input', recalc);
document.getElementById('discountInput').addEventListener('input', recalc);
bindRow(document.querySelector('.item-row'));
recalc();

document.getElementById('saleForm').addEventListener('submit', function (e) {
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