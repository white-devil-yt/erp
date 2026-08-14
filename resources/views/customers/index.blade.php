@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-people me-2 text-primary"></i>Customers</h4>
    <a href="{{ route('customers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Customer</a>
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
        @if ($customers->isEmpty())
            <div class="empty-state"><i class="bi bi-people"></i><p class="mb-0">No customers found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th class="text-center">Invoices</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white me-2">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                        <div>
                                            <a href="{{ route('customers.show', $customer) }}" class="fw-semibold text-decoration-none">{{ $customer->name }}</a>
                                            <span class="d-block small text-muted">{{ $customer->gst_number ?? 'No GST' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="small">
                                    <span class="d-block">{{ $customer->email ?? '—' }}</span>
                                    <span class="text-muted">{{ $customer->phone ?? '—' }}</span>
                                </td>
                                <td>{{ $customer->company ?? '—' }}</td>
                                <td class="text-center"><span class="badge bg-soft-primary">{{ $customer->sales_count }}</span></td>
                                <td>
                                    @if ($customer->is_active)
                                        <span class="badge bg-soft-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
@endsection