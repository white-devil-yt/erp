<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f6fb; padding: 30px; }
        .invoice-sheet { background: #fff; max-width: 800px; margin: 0 auto; padding: 40px; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .invoice-header { border-bottom: 3px solid #6366f1; padding-bottom: 20px; margin-bottom: 24px; }
        .brand { font-size: 1.4rem; font-weight: 800; color: #1a1d2e; }
        .brand i { color: #6366f1; }
        .muted { color: #64748b; font-size: 0.8rem; }
        .amount-row td { border: none; }
        .total-row td { border-top: 2px solid #e2e8f0 !important; font-weight: 800; }
        @media print {
            body { background: #fff; padding: 0; }
            .invoice-sheet { box-shadow: none; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="invoice-sheet">
    @php($cur = setting('currency_symbol', '₹'))
    <div class="text-center mb-3 no-print">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print / Save PDF</button>
        <button class="btn btn-light" onclick="window.close()">Close</button>
    </div>

    <div class="invoice-header d-flex justify-content-between align-items-end">
        <div>
            <div class="brand">
                @if (setting('company_logo'))
                    <img src="{{ asset('storage/' . setting('company_logo')) }}" alt="Logo" style="max-height:48px;" class="me-2 align-middle">
                @else
                    <i class="bi bi-boxes me-1"></i>
                @endif
                {{ setting('company_name', config('app.name')) }}
            </div>
            <div class="muted mt-1">
                {{ setting('company_address') }}<br>
                {{ setting('company_email') }} • {{ setting('company_phone') }}
                @if (setting('invoice_show_gst', '1'))
                    <br>GST: {{ setting('company_gst') }}
                @endif
            </div>
        </div>
        <div class="text-end">
            <h2 class="fw-bold text-primary mb-1">INVOICE</h2>
            <div class="muted">
                <strong class="text-dark d-block">{{ $sale->invoice_number }}</strong>
                Date: {{ $sale->invoice_date->format('d M Y') }}<br>
                Due: {{ $sale->due_date?->format('d M Y') ?? '—' }}
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-6">
            <div class="muted mb-1">BILL TO</div>
            @if ($sale->customer)
                <h6 class="fw-bold mb-0">{{ $sale->customer->name }}</h6>
                <div class="muted">
                    {{ $sale->customer->company }}<br>
                    {{ $sale->customer->address }}<br>
                    {{ $sale->customer->phone }} • {{ $sale->customer->email }}
                </div>
            @else
                <h6 class="fw-bold mb-0">Walk-in Customer</h6>
            @endif
        </div>
        <div class="col-6 text-end">
            <div class="muted mb-1">STATUS</div>
            @if ($sale->payment_status === 'paid')
                <span class="badge bg-success">Paid</span>
            @elseif ($sale->payment_status === 'partial')
                <span class="badge bg-warning text-dark">Partial Payment</span>
            @else
                <span class="badge bg-danger">Unpaid</span>
            @endif
        </div>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width:5%">#</th>
                <th>Product</th>
                <th class="text-center" style="width:10%">Qty</th>
                <th class="text-end" style="width:15%">Unit Price</th>
                <th class="text-end" style="width:15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong class="d-block">{{ $item->product->name }}</strong>
                        <span class="muted">{{ $item->product->sku }}</span>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ $cur }} {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ $cur }} {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-between">
        <div class="muted align-self-end">
            @if ($sale->notes)
                <strong class="text-dark d-block mb-1">Notes</strong>{{ $sale->notes }}
            @endif
        </div>
        <table class="table" style="max-width:320px">
            <tr class="amount-row">
                <td class="muted">Subtotal</td>
                <td class="text-end">{{ $cur }} {{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            <tr class="amount-row">
                <td class="muted">Tax ({{ $sale->tax_rate }}%)</td>
                <td class="text-end">{{ $cur }} {{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
            <tr class="amount-row">
                <td class="muted">Discount</td>
                <td class="text-end text-danger">-{{ $cur }} {{ number_format($sale->discount, 2) }}</td>
            </tr>
            <tr class="amount-row">
                <td class="muted">Amount Paid</td>
                <td class="text-end text-success">{{ $cur }} {{ number_format($sale->amount_paid, 2) }}</td>
            </tr>
            <tr class="amount-row">
                <td class="muted">Balance Due</td>
                <td class="text-end {{ $sale->balance_due > 0 ? 'text-danger' : 'text-success' }}">{{ $cur }} {{ number_format($sale->balance_due, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="fs-5">GRAND TOTAL</td>
                <td class="text-end fs-5 text-primary">{{ $cur }} {{ number_format($sale->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="text-center muted mt-4 pt-3 border-top">
        {{ setting('invoice_footer', 'Thank you for your business!') }} • Generated on {{ now()->format('d M Y, h:i A') }}
    </div>
    @if (setting('invoice_terms'))
        <div class="muted mt-3 pt-2 border-top">
            <strong class="text-dark d-block mb-1">Terms & Conditions</strong>{{ setting('invoice_terms') }}
        </div>
    @endif
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script>
window.onload = function () { if (!window.opener) setTimeout(() => window.print(), 300); };
</script>
</body>
</html>