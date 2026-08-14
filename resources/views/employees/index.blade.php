@extends('layouts.app')

@section('title', 'Employees')
@section('page-title', 'Employees')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person-badge me-2 text-primary"></i>Employees</h4>
    <a href="{{ route('employees.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Employee</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, code or position...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($employees->isEmpty())
            <div class="empty-state"><i class="bi bi-person-badge"></i><p class="mb-0">No employees found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th class="text-end">Net Salary</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employees as $employee)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white me-2">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                                        <div>
                                            <a href="{{ route('employees.show', $employee) }}" class="fw-semibold text-decoration-none">{{ $employee->name }}</a>
                                            <span class="d-block small text-muted">{{ $employee->phone ?? '—' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $employee->employee_code }}</td>
                                <td>{{ $employee->department->name ?? '—' }}</td>
                                <td>{{ $employee->position ?? '—' }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($employee->basic_salary + $employee->allowances - $employee->deductions, 2) }}</td>
                                <td>
                                    @if ($employee->status === 'active')
                                        <span class="badge bg-soft-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this employee?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $employees->links() }}</div>
        @endif
    </div>
</div>
@endsection