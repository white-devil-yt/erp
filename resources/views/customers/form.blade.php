@extends('layouts.app')

@section('title', $customer->exists ? 'Edit Customer' : 'New Customer')
@section('page-title', $customer->exists ? 'Edit Customer' : 'New Customer')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>{{ $customer->exists ? 'Edit Customer' : 'Create Customer' }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $customer->exists ? route('customers.update', $customer) : route('customers.store') }}">
                    @csrf
                    @if ($customer->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $customer->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company', $customer->company) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $customer->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $customer->gst_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1" {{ old('is_active', $customer->is_active) ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !old('is_active', $customer->is_active) ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="2" class="form-control">{{ old('notes', $customer->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>{{ $customer->exists ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection