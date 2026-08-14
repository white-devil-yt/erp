@extends('layouts.app')

@section('title', 'Payroll Report')
@section('page-title', 'Payroll Report')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-receipt me-2 text-primary"></i>Payroll Report</h4>
    <button class="btn btn-light" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Month</label>
                <select name="month" class="form-select">
                    @foreach ($months as $m)
                        <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('Y-m', $m)->format('F Y') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold">{{ $summary['count'] }}</div><div class="small text-muted">Employees</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold">₹{{ number_format($summary['basic']) }}</div><div class="small text-muted">Basic</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-success">₹{{ number_format($summary['allowances']) }}</div><div class="small text-muted">Allowances</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-danger">-₹{{ number_format($summary['deductions']) }}</div><div class="small text-muted">Deductions</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-warning">+₹{{ number_format($summary['bonus'] + $summary['overtime']) }}</div><div class="small text-muted">Bonus + OT</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-5 fw-bold text-primary">₹{{ number_format($summary['net']) }}</div><div class="small text-muted">Net Payroll</div>
    </div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Employee Payslips — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</h6>
            </div>
            <div class="card-body p-0">
                @if ($payslips->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No payslips for this month</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-end">Basic</th>
                                    <th class="text-end">Allowances</th>
                                    <th class="text-end">Deductions</th>
                                    <th class="text-end">Net</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payslips as $payslip)
                                    <tr>
                                        <td>
                                            <a href="{{ route('payslips.print', $payslip) }}" target="_blank" class="fw-semibold text-decoration-none">{{ $payslip->employee->name }}</a>
                                            <span class="d-block small text-muted">{{ $payslip->employee->employee_code }}</span>
                                        </td>
                                        <td>{{ $payslip->employee->department->name ?? '—' }}</td>
                                        <td class="text-center">{{ $payslip->present_days }}/{{ $payslip->total_working_days }}</td>
                                        <td class="text-end">₹{{ number_format($payslip->basic_salary, 2) }}</td>
                                        <td class="text-end">₹{{ number_format($payslip->allowances, 2) }}</td>
                                        <td class="text-end text-danger">-₹{{ number_format($payslip->deductions, 2) }}</td>
                                        <td class="text-end fw-semibold text-primary">₹{{ number_format($payslip->net_salary, 2) }}</td>
                                        <td>
                                            @if ($payslip->status === 'paid')
                                                <span class="badge bg-soft-success">Paid</span>
                                            @else
                                                <span class="badge bg-soft-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="3">TOTAL</td>
                                    <td class="text-end">₹{{ number_format($summary['basic'], 2) }}</td>
                                    <td class="text-end">₹{{ number_format($summary['allowances'], 2) }}</td>
                                    <td class="text-end">-₹{{ number_format($summary['deductions'], 2) }}</td>
                                    <td class="text-end">₹{{ number_format($summary['net'], 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2 text-primary"></i>Cost by Department</h6>
            </div>
            <div class="card-body">
                @if ($byDepartment->isEmpty())
                    <div class="empty-state"><i class="bi bi-inbox"></i>No data</div>
                @else
                    <ul class="list-group list-group-flush mb-3">
                        @foreach ($byDepartment as $name => $dept)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-semibold small">{{ $name }}</span>
                                <span>
                                    <span class="badge bg-soft-secondary me-2">{{ $dept['count'] }} emp</span>
                                    <span class="badge bg-soft-primary">₹{{ number_format($dept['total'], 2) }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <canvas id="deptChart" height="180"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const depts = @json($byDepartment);
new Chart(document.getElementById('deptChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(depts),
        datasets: [{
            data: Object.values(depts).map(d => d.total),
            backgroundColor: ['#6366f1', '#10b981', '#0ea5e9', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 10 } } }
    }
});
</script>
@endpush