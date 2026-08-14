@extends('layouts.app')

@section('title', 'Journal Entry #' . $entry->id)
@section('page-title', 'Journal Entry #' . $entry->id)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-journal-text me-2 text-primary"></i>Journal Entry #{{ $entry->id }}</h4>
    <a href="{{ route('accounting.journal') }}" class="btn btn-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row">
            <div class="col-md-4">
                <span class="d-block text-muted small text-uppercase fw-semibold">Date</span>
                <strong>{{ $entry->date->format('d M Y') }}</strong>
            </div>
            <div class="col-md-4">
                <span class="d-block text-muted small text-uppercase fw-semibold">Type</span>
                <span class="badge bg-soft-primary">{{ $entry->type }}</span>
            </div>
            <div class="col-md-4">
                <span class="d-block text-muted small text-uppercase fw-semibold">Created By</span>
                <strong>{{ $entry->user->name ?? 'System' }}</strong>
            </div>
            <div class="col-12 mt-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Description</span>
                <strong>{{ $entry->description }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-list-columns me-2 text-primary"></i>Entry Lines</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Account</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entry->lines as $line)
                        <tr>
                            <td class="text-muted">{{ $line->account->code }}</td>
                            <td>
                                <a href="{{ route('accounting.ledger', $line->account) }}" class="text-decoration-none">{{ $line->account->name }}</a>
                            </td>
                            <td class="text-end">{{ $line->debit > 0 ? currency($line->debit) : '—' }}</td>
                            <td class="text-end">{{ $line->credit > 0 ? currency($line->credit) : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">Totals</td>
                        <td class="text-end">{{ currency($entry->totalDebit()) }}</td>
                        <td class="text-end">{{ currency($entry->totalCredit()) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection