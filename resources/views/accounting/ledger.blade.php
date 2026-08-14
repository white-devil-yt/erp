@extends('layouts.app')

@section('title', 'Ledger - ' . $account->name)
@section('page-title', 'Ledger - ' . $account->name)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-list-columns me-2 text-primary"></i>{{ $account->code }} · {{ $account->name }}</h4>
    <span class="badge bg-soft-primary">{{ $account->typeLabel() }}</span>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card"><div class="card-body py-3">
            <span class="d-block text-muted small text-uppercase fw-semibold">Opening Balance</span>
            <span class="fs-5 fw-bold">{{ currency($opening) }}</span>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card"><div class="card-body py-3">
            <span class="d-block text-muted small text-uppercase fw-semibold">Debits</span>
            <span class="fs-5 fw-bold text-danger">{{ currency($lines->sum(fn ($l) => $l->debit)) }}</span>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card"><div class="card-body py-3">
            <span class="d-block text-muted small text-uppercase fw-semibold">Credits</span>
            <span class="fs-5 fw-bold text-success">{{ currency($lines->sum(fn ($l) => $l->credit)) }}</span>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card"><div class="card-body py-3">
            <span class="d-block text-muted small text-uppercase fw-semibold">Closing Balance</span>
            <span class="fs-5 fw-bold">{{ currency($opening + $lines->sum(fn ($l) => $l->debit - $l->credit)) }}</span>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($lines->isEmpty())
            <div class="empty-state"><i class="bi bi-list-columns"></i><p class="mb-0">No activity in this account.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lines as $line)
                            <tr>
                                <td class="text-muted">{{ $line->entry->date->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('accounting.journal.show', $line->entry) }}" class="text-decoration-none">{{ $line->entry->description }}</a>
                                    <span class="d-block small text-muted">#{{ $line->entry_id }} · {{ $line->entry->type }}</span>
                                </td>
                                <td class="text-end">{{ $line->debit > 0 ? currency($line->debit) : '—' }}</td>
                                <td class="text-end">{{ $line->credit > 0 ? currency($line->credit) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $lines->links() }}</div>
        @endif
    </div>
</div>
@endsection