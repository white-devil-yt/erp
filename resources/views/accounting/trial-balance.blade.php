@extends('layouts.app')

@section('title', 'Trial Balance')
@section('page-title', 'Trial Balance')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-columns-gap me-2 text-primary"></i>Trial Balance</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($lines->isEmpty())
            <div class="empty-state"><i class="bi bi-columns-gap"></i><p class="mb-0">No journal entries posted yet.</p></div>
        @else
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
                        @foreach ($lines as $line)
                            <tr>
                                <td class="text-muted">{{ $line['account']->code }}</td>
                                <td>
                                    {{ $line['account']->name }}
                                    <span class="badge bg-soft-secondary ms-1">{{ $line['account']->typeLabel() }}</span>
                                </td>
                                <td class="text-end">{{ $line['debit'] > 0 ? currency($line['debit']) : '—' }}</td>
                                <td class="text-end">{{ $line['credit'] > 0 ? currency($line['credit']) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="2" class="text-end">Totals</td>
                            <td class="text-end">{{ currency($totalDebit) }}</td>
                            <td class="text-end">{{ currency($totalCredit) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="{{ abs($totalDebit - $totalCredit) < 0.01 ? 'text-success' : 'text-danger' }} fw-semibold">
                                {{ abs($totalDebit - $totalCredit) < 0.01 ? '✓ Trial balance is balanced' : '⚠ Trial balance difference: ' . currency(abs($totalDebit - $totalCredit)) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection