@extends('layouts.app')

@section('title', $department->exists ? 'Edit Department' : 'New Department')
@section('page-title', $department->exists ? 'Edit Department' : 'New Department')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2 text-primary"></i>{{ $department->exists ? 'Edit Department' : 'Create Department' }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $department->exists ? route('departments.update', $department) : route('departments.store') }}">
                    @csrf
                    @if ($department->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label">Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $department->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control">{{ old('description', $department->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>{{ $department->exists ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('departments.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection