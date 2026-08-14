@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-diagram-3 me-2 text-primary"></i>Departments</h4>
    <a href="{{ route('departments.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Department</a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($departments->isEmpty())
            <div class="empty-state"><i class="bi bi-diagram-3"></i><p class="mb-0">No departments found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="text-center">Employees</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($departments as $department)
                            <tr>
                                <td>{{ $department->id }}</td>
                                <td><strong>{{ $department->name }}</strong></td>
                                <td class="text-muted">{{ Str::limit($department->description, 60) }}</td>
                                <td class="text-center"><span class="badge bg-soft-primary">{{ $department->employees_count }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $departments->links() }}</div>
        @endif
    </div>
</div>
@endsection