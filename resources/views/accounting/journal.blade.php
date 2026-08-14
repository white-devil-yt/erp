@extends('layouts.app')

@section('title', 'Journal Entries')
@section('page-title', 'Journal Entries')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-journal-text me-2 text-primary"></i>Journal Entries</h4>
    <a href="{{ route('accounting.journal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Entry</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>Sale</option>
                    <option value="purchase" {{ request('type') === 'purchase' ? 'selected' : '' }}>Purchase</option>
                    <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
                    <option value="payroll" {{ request('type') === 'payroll' ? 'selected' : '' }}>Payroll</option>
                    <option value="journal" {{ request('type') === 'journal' ? 'selected' : '' }}>Manual</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($entries->isEmpty())
            <div class="empty-state"><i class="bi bi-journal-text"></i><p class="mb-0">No journal entries found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $entry->id }}</td>
                                <td>{{ $entry->date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('accounting.journal.show', $entry) }}" class="fw-semibold text-decoration-none">{{ $entry->description }}</a>
                                    <span class="d-block small text-muted">By {{ $entry->user->name ?? 'System' }}</span>
                                </td>
                                <td><span class="badge bg-soft-primary">{{ $entry->type }}</span></td>
                                <td class="text-end">{{ currency($entry->totalDebit()) }}</td>
                                <td class="text-end">{{ currency($entry->totalCredit()) }}</td>
                                <td class="text-end">
                                    <a href="{{ route('accounting.journal.show', $entry) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $entries->links() }}</div>
        @endif
    </div>
</div>
@endsection