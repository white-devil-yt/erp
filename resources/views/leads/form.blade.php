@extends('layouts.app')

@section('title', $lead->exists ? 'Edit Lead' : 'New Lead')
@section('page-title', $lead->exists ? 'Edit Lead' : 'New Lead')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>{{ $lead->exists ? 'Edit Lead' : 'Create Lead' }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $lead->exists ? route('leads.update', $lead) : route('leads.store') }}">
                    @csrf
                    @if ($lead->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $lead->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company</label>
                            <input type="text" name="company" class="form-control" value="{{ old('company', $lead->company) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $lead->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Source</label>
                            <select name="source" class="form-select">
                                @foreach (\App\Models\Lead::SOURCES as $key => $label)
                                    <option value="{{ $key }}" {{ old('source', $lead->source) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach (\App\Models\Lead::STATUSES as $key => $label)
                                    <option value="{{ $key }}" {{ old('status', $lead->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Value (₹)</label>
                            <input type="number" step="0.01" min="0" name="value" class="form-control"
                                   value="{{ old('value', $lead->value) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Expected Close Date</label>
                            <input type="date" name="expected_close_date" class="form-control"
                                   value="{{ old('expected_close_date', $lead->expected_close_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Assigned To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Contacted</label>
                            <input type="date" name="last_contacted_at" class="form-control"
                                   value="{{ old('last_contacted_at', $lead->last_contacted_at?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $lead->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>{{ $lead->exists ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('leads.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection