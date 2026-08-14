@extends('layouts.app')

@section('title', 'Payslips')
@section('page-title', 'Payslips')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-cash-coin me-2 text-primary"></i>Payslips</h4>
    <form method="GET" action="{{ route('payslips.generate') }}" class="d-flex gap-2 align-items-center">
        <input type="month" name="month" value="{{ $month }}" class="form-control" style="width:auto">
        <button type="submit" class="btn btn-primary"><i class="bi bi-magic me-1"></i>Generate for Month</button>
    </form>
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
            <div class="col-md-3">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="generated" {{ request('status') === 'generated' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-md-3 text-md-end">
                <div class="small text-muted">Month payroll: <strong class="text-primary">₹{{ number_format($totalPayroll, 2) }}</strong><br>
                Paid so far: <strong class="text-success">₹{{ number_format($totalPaid, 2) }}</strong></div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($payslips->isEmpty())
            <div class="empty-state">
                <i class="bi bi-cash-stack"></i>
                <p class="mb-1">No payslips for this month.</p>
                <p class="small text-muted">Click "Generate for Month" to create payslips from employee salaries and attendance.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th class="text-center">Present</th>
                            <th class="text-end">Basic</th>
                            <th class="text-end">Allowances</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net Salary</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payslips as $payslip)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white me-2">{{ strtoupper(substr($payslip->employee->name, 0, 1)) }}</div>
                                        <div>
                                            <strong class="d-block">{{ $payslip->employee->name }}</strong>
                                            <span class="small text-muted">{{ $payslip->employee->position }}</span>
                                        </div>
                                    </div>
                                </td>
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
                                <td class="text-end">
                                    <a href="{{ route('payslips.print', $payslip) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-secondary" title="Print"><i class="bi bi-printer"></i></a>
                                    @if ($payslip->status !== 'paid')
                                        <form action="{{ route('payslips.mark-paid', $payslip) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Mark as Paid"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $payslips->links() }}</div>
        @endif
    </div>
</div>
@endsection