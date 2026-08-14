@extends('layouts.app')

@section('title', $employee->name)
@section('page-title', $employee->name)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person me-2 text-primary"></i>Employee Profile</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('payslips.index') }}" class="btn btn-light"><i class="bi bi-cash-stack me-1"></i>Payslips</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <div class="avatar mx-auto mb-3 bg-primary text-white" style="width:64px;height:64px;font-size:1.6rem">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $employee->name }}</h5>
                <p class="text-muted small mb-2">{{ $employee->position ?? 'Employee' }} • {{ $employee->department->name ?? 'No Dept' }}</p>
                @if ($employee->status === 'active')
                    <span class="badge bg-soft-success">Active</span>
                @else
                    <span class="badge bg-soft-secondary">Inactive</span>
                @endif
            </div>
            <div class="card-body pt-0">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Employee Code</span>
                    <strong>{{ $employee->employee_code }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Email</span>
                    <span class="small">{{ $employee->email ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Phone</span>
                    <span class="small">{{ $employee->phone ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Joining Date</span>
                    <span>{{ $employee->joining_date?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Basic Salary</span>
                    <strong>₹{{ number_format($employee->basic_salary, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Allowances</span>
                    <strong>₹{{ number_format($employee->allowances, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Deductions</span>
                    <strong>₹{{ number_format($employee->deductions, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted">Net Salary</span>
                    <strong class="text-success">₹{{ number_format($employee->basic_salary + $employee->allowances - $employee->deductions, 2) }}</strong>
                </div>
                @if ($employee->bank_name)
                    <div class="mt-3 p-3 bg-light rounded-3 small">
                        <strong class="d-block mb-1 text-muted text-uppercase" style="font-size:0.7rem">Bank Details</strong>
                        {{ $employee->bank_name }}<br>
                        A/C: {{ $employee->account_number }}<br>
                        IFSC: {{ $employee->ifsc_code }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Recent Attendance</h6>
            </div>
            <div class="card-body p-0">
                @if ($recentAttendance->isEmpty())
                    <div class="empty-state"><i class="bi bi-calendar-x"></i>No attendance records</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAttendance as $att)
                                    <tr>
                                        <td>{{ $att->date->format('d M Y') }}</td>
                                        <td>
                                            @if ($att->status === 'present')
                                                <span class="badge bg-soft-success">Present</span>
                                            @elseif ($att->status === 'absent')
                                                <span class="badge bg-soft-danger">Absent</span>
                                            @elseif ($att->status === 'half_day')
                                                <span class="badge bg-soft-warning">Half Day</span>
                                            @else
                                                <span class="badge bg-soft-info">Leave</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ $att->check_in ?? '—' }}</td>
                                        <td class="small">{{ $att->check_out ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-cash-stack me-2 text-primary"></i>Payslips</h6>
                <a href="{{ route('payslips.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if ($recentPayslips->isEmpty())
                    <div class="empty-state"><i class="bi bi-cash"></i>No payslips yet. Generate from the Payslips page.</div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Present</th>
                                    <th class="text-end">Net Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentPayslips as $payslip)
                                    <tr>
                                        <td>
                                            <a href="{{ route('payslips.print', $payslip) }}" target="_blank" class="fw-semibold text-decoration-none">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $payslip->month)->format('M Y') }}
                                            </a>
                                        </td>
                                        <td class="text-end">{{ $payslip->present_days }}/{{ $payslip->total_working_days }}</td>
                                        <td class="text-end fw-semibold">₹{{ number_format($payslip->net_salary, 2) }}</td>
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
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection