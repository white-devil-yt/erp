@extends('layouts.app')

@section('title', 'Attendance')
@section('page-title', 'Attendance')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-calendar-check me-2 text-primary"></i>Daily Attendance</h4>
    <form method="GET" class="d-flex gap-2 align-items-center">
        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-control" style="width:auto">
        <button type="submit" class="btn btn-primary"><i class="bi bi-calendar3 me-1"></i>Load Day</button>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-4 fw-bold text-success">{{ $summary['present'] }}</div>
        <div class="small text-muted">Present</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-4 fw-bold text-danger">{{ $summary['absent'] }}</div>
        <div class="small text-muted">Absent</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-4 fw-bold text-warning">{{ $summary['half_day'] }}</div>
        <div class="small text-muted">Half Day</div>
    </div></div></div>
    <div class="col-md-2 col-4"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-4 fw-bold text-info">{{ $summary['leave'] }}</div>
        <div class="small text-muted">Leave</div>
    </div></div></div>
    <div class="col-md-4 col-8"><div class="card"><div class="card-body text-center py-3">
        <div class="fs-4 fw-bold text-primary">{{ $summary['not_marked'] }}</div>
        <div class="small text-muted">Not Marked</div>
    </div></div></div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-people me-2 text-primary"></i>Mark Attendance — {{ $date->format('l, d M Y') }}</h6>
    </div>
    <div class="card-body p-0">
        @if ($employees->isEmpty())
            <div class="empty-state"><i class="bi bi-people"></i>No active employees</div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th style="width:300px">Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            @php $att = $employee->attendance->first(); @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white me-2">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                                        <div>
                                            <strong class="d-block">{{ $employee->name }}</strong>
                                            <span class="small text-muted">{{ $employee->employee_code }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->department->name ?? '—' }}</td>
                                <td class="small">{{ $att->check_in ?? '—' }}</td>
                                <td class="small">{{ $att->check_out ?? '—' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('attendance.mark') }}" class="d-flex gap-1">
                                        @csrf
                                        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        @foreach (['present' => 'success', 'absent' => 'danger', 'half_day' => 'warning', 'leave' => 'info'] as $value => $color)
                                            <button type="submit" name="status" value="{{ $value }}"
                                                    class="btn btn-sm btn-{{ ($att->status ?? '') === $value ? '' : 'outline-' }}{{ $color }} px-2">
                                                {{ str_replace('_', ' ', ucfirst($value)) }}
                                            </button>
                                        @endforeach
                                    </form>
                                </td>
                                <td class="text-end">
                                    @if ($att)
                                        <form action="{{ route('attendance.destroy', $att) }}" method="POST" onsubmit="return confirm('Remove this entry?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Remove"><i class="bi bi-x-lg"></i></button>
                                        </form>
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
@endsection