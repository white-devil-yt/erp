@extends('layouts.app')

@section('title', 'Categories')
@section('page-title', 'Product Categories')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-tags me-2 text-primary"></i>Categories</h4>
    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Category</a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($categories->isEmpty())
            <div class="empty-state">
                <i class="bi bi-tags"></i>
                <p class="mb-0">No categories found. Create your first category!</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="text-center">Products</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td class="text-muted">{{ Str::limit($category->description, 60) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary">{{ $category->products_count }}</span>
                                </td>
                                <td class="text-muted small">{{ $category->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection