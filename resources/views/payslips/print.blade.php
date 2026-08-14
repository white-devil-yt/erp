<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip — {{ $payslip->employee->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f6fb; padding: 30px; }
        .slip { background: #fff; max-width: 800px; margin: 0 auto; padding: 40px; border-radius: 8px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .slip-header { border-bottom: 3px solid #10b981; padding-bottom: 20px; margin-bottom: 24px; }
        .brand { font-size: 1.4rem; font-weight: 800; color: #1a1d2e; }
        .brand i { color: #10b981; }
        .muted { color: #64748b; font-size: 0.8rem; }
        .earn td, .deduct td { border: none; }
        .net-row td { border-top: 2px solid #10b981 !important; font-weight: 800; background: #f0fdf4; }
        @media print {
            body { background: #fff; padding: 0; }
            .slip { box-shadow: none; padding: 20px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="slip">
    <div class="text-center mb-3 no-print">
        <button class="btn btn-success" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print / Save PDF</button>
        <button class="btn btn-light" onclick="window.close()">Close</button>
    </div>

    <div class="slip-header d-flex justify-content-between align-items-end">
        <div>
            <div class="brand"><i class="bi bi-boxes me-1"></i>{{ config('app.name') }}</div>
            <div class="muted mt-1">123 Business Park, Main Street, Mumbai, India<br>info@company.com • +91 98765 43210</div>
        </div>
        <div class="text-end">
            <h2 class="fw-bold text-success mb-1">PAYSLIP</h2>
            <div class="muted">
                <strong class="text-dark d-block">{{ \Carbon\Carbon::createFromFormat('Y-m', $payslip->month)->format('F Y') }}</strong>
                Generated: {{ $payslip->created_at->format('d M Y') }}
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-7">
            <div class="muted mb-1">EMPLOYEE</div>
            <h6 class="fw-bold mb-0">{{ $payslip->employee->name }}</h6>
            <div class="muted">
                {{ $payslip->employee->employee_code }} • {{ $payslip->employee->position }}<br>
                Department: {{ $payslip->employee->department->name ?? '—' }}<br>
                Bank: {{ $payslip->employee->bank_name ?? '—' }} ({{ $payslip->employee->account_number ?? '—' }})
            </div>
        </div>
        <div class="col-5 text-end">
            <div class="muted mb-1">ATTENDANCE</div>
            <h6 class="fw-bold mb-0">Present: {{ $payslip->present_days }} / {{ $payslip->total_working_days }} days</h6>
            <div class="muted">
                @if ($payslip->status === 'paid')
                    <span class="badge bg-success">PAID {{ $payslip->paid_date?->format('d M Y') }}</span>
                @else
                    <span class="badge bg-warning text-dark">PENDING</span>
                @endif
            </div>
        </div>
    </div>

    <table class="table">
        <thead class="table-light">
            <tr><th colspan="2">EARNINGS</th></tr>
        </thead>
        <tbody>
            <tr class="earn"><td class="muted">Basic Salary</td><td class="text-end">₹{{ number_format($payslip->basic_salary, 2) }}</td></tr>
            <tr class="earn"><td class="muted">Allowances</td><td class="text-end">₹{{ number_format($payslip->allowances, 2) }}</td></tr>
            @if ($payslip->overtime_amount > 0)
                <tr class="earn"><td class="muted">Overtime</td><td class="text-end">₹{{ number_format($payslip->overtime_amount, 2) }}</td></tr>
            @endif
            @if ($payslip->bonus > 0)
                <tr class="earn"><td class="muted">Bonus</td><td class="text-end">₹{{ number_format($payslip->bonus, 2) }}</td></tr>
            @endif
            <tr class="earn"><td class="muted">Gross Earnings</td><td class="text-end fw-semibold">₹{{ number_format($payslip->basic_salary + $payslip->allowances + $payslip->overtime_amount + $payslip->bonus, 2) }}</td></tr>
        </tbody>
        <thead class="table-light">
            <tr><th colspan="2">DEDUCTIONS</th></tr>
        </thead>
        <tbody>
            <tr class="deduct"><td class="muted">Deductions</td><td class="text-end text-danger">-₹{{ number_format($payslip->deductions, 2) }}</td></tr>
            <tr class="net-row">
                <td class="fs-5">NET PAYABLE</td>
                <td class="text-end fs-5 text-success">₹{{ number_format($payslip->net_salary, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="row mt-4">
        <div class="col-6">
            <div class="muted mb-4">This is a computer generated payslip and does not require a signature.</div>
        </div>
        <div class="col-6 text-end">
            <div class="muted">Authorized Signatory</div>
            <div class="border-top border-2 mt-4 pt-1 fw-semibold">For {{ config('app.name') }}</div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script>
window.onload = function () { if (!window.opener) setTimeout(() => window.print(), 300); };
</script>
</body>
</html>