@extends('layouts.app')

@section('title', $category->exists ? 'Edit Category' : 'New Category')
@section('page-title', $category->exists ? 'Edit Category' : 'New Category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-tags me-2 text-primary"></i>{{ $category->exists ? 'Edit Category' : 'Create Category' }}</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
                    @csrf
                    @if ($category->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $category->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                  placeholder="What kind of products does this category hold?">{{ old('description', $category->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>{{ $category->exists ? 'Update' : 'Create' }}
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection