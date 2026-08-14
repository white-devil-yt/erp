@extends('layouts.app')

@section('title', $employee->exists ? 'Edit Employee' : 'New Employee')
@section('page-title', $employee->exists ? 'Edit Employee' : 'New Employee')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>{{ $employee->exists ? 'Edit Employee' : 'Create Employee' }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $employee->exists ? route('employees.update', $employee) : route('employees.store') }}">
                    @csrf
                    @if ($employee->exists) @method('PUT') @endif

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person me-2"></i>Personal Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $employee->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Joining Date</label>
                            <input type="date" name="joining_date" class="form-control"
                                   value="{{ old('joining_date', $employee->joining_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">No Department</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Position</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position', $employee->position) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $employee->address) }}</textarea>
                        </div>
                        @if (!$employee->exists)
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_login" value="1" id="createLogin">
                                    <label class="form-check-label small" for="createLogin">
                                        Create employee login (default password: <code>password</code>)
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-cash-stack me-2"></i>Salary Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Basic Salary (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror"
                                   value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                            @error('basic_salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Allowances (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="allowances" class="form-control" value="{{ old('allowances', $employee->allowances) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deductions (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="deductions" class="form-control" value="{{ old('deductions', $employee->deductions) }}" required>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-bank me-2"></i>Bank Details</h6>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $employee->account_number) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $employee->ifsc_code) }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>{{ $employee->exists ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('employees.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection