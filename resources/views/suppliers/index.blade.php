@extends('layouts.app')

@section('title', 'Suppliers')
@section('page-title', 'Suppliers')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-truck me-2 text-primary"></i>Suppliers</h4>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Supplier</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name, email, phone or company...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Search</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($suppliers->isEmpty())
            <div class="empty-state"><i class="bi bi-truck"></i><p class="mb-0">No suppliers found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th class="text-center">Purchases</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-success text-white me-2">{{ strtoupper(substr($supplier->name, 0, 1)) }}</div>
                                        <div>
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="fw-semibold text-decoration-none">{{ $supplier->name }}</a>
                                            <span class="d-block small text-muted">{{ $supplier->gst_number ?? 'No GST' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">
                                    <span class="d-block">{{ $supplier->email ?? '—' }}</span>
                                    <span class="text-muted">{{ $supplier->phone ?? '—' }}</span>
                                </td>
                                <td>{{ $supplier->company ?? '—' }}</td>
                                <td class="text-center"><span class="badge bg-soft-primary">{{ $supplier->purchases_count }}</span></td>
                                <td>
                                    @if ($supplier->is_active)
                                        <span class="badge bg-soft-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this supplier?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $suppliers->links() }}</div>
        @endif
    </div>
</div>
@endsection