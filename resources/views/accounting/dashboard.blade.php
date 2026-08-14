@extends('layouts.app')

@section('title', 'Accounting Dashboard')
@section('page-title', 'Accounting Dashboard')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-calculator me-2 text-primary"></i>Accounting</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('accounting.journal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Journal Entry</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Cash & Bank</span>
                <span class="fs-4 fw-bold">{{ currency($cash) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Receivables</span>
                <span class="fs-4 fw-bold text-danger">{{ currency($receivable) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Payables</span>
                <span class="fs-4 fw-bold text-danger">{{ currency($payable) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Inventory Value</span>
                <span class="fs-4 fw-bold">{{ currency($inventory) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-4">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Total Assets</span>
                <span class="fs-4 fw-bold">{{ currency($assets) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Total Liabilities</span>
                <span class="fs-4 fw-bold text-danger">{{ currency($liabilities) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Net Equity</span>
                <span class="fs-4 fw-bold text-success">{{ currency($equity) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-4">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Total Income</span>
                <span class="fs-4 fw-bold text-success">{{ currency($income) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Total Expenses</span>
                <span class="fs-4 fw-bold text-danger">{{ currency($expenses) }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="card">
            <div class="card-body py-3">
                <span class="d-block text-muted small text-uppercase fw-semibold">Net Profit</span>
                <span class="fs-4 fw-bold {{ $income - $expenses >= 0 ? 'text-success' : 'text-danger' }}">{{ currency($income - $expenses) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-lightning-charge me-2 text-primary"></i>Quick Reports</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('accounting.trial-balance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Trial Balance <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accounting.income-statement') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Income Statement (P&L) <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accounting.balance-sheet') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Balance Sheet <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accounting.accounts') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Chart of Accounts <i class="bi bi-chevron-right"></i>
                    </a>
                    <a href="{{ route('accounting.journal') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        Journal Entries <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Journal Entries</h6>
            </div>
            <div class="card-body p-0">
                @if ($recentEntries->isEmpty())
                    <div class="empty-state"><i class="bi bi-journal"></i><p class="mb-0">No journal entries yet.</p></div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($recentEntries as $entry)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('accounting.journal.show', $entry) }}" class="fw-semibold text-decoration-none small">#{{ $entry->id }} · {{ $entry->description }}</a>
                                    <span class="d-block small text-muted">{{ $entry->date->format('d M Y') }} · {{ $entry->type }}</span>
                                </div>
                                <span class="small text-muted">{{ $entry->user->name ?? 'System' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection