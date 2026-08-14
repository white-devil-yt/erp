@extends('layouts.app')

@section('title', 'Income Statement')
@section('page-title', 'Income Statement')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Income Statement (Profit & Loss)</h4>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Apply</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-success"><i class="bi bi-arrow-up-circle me-2"></i>Income</h6>
            </div>
            <div class="card-body p-0">
                @if ($income->isEmpty())
                    <div class="empty-state"><i class="bi bi-arrow-up-circle"></i><p class="mb-0">No income recorded.</p></div>
                @else
                    <table class="table mb-0">
                        <tbody>
                            @foreach ($income as $row)
                                <tr>
                                    <td>
                                        {{ $row['account']->name }}
                                        <span class="d-block small text-muted">{{ $row['account']->code }}</span>
                                    </td>
                                    <td class="text-end fw-semibold text-success">{{ currency($row['amount']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td>Total Income</td>
                                <td class="text-end text-success">{{ currency($totalIncome) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-danger"><i class="bi bi-arrow-down-circle me-2"></i>Expenses</h6>
            </div>
            <div class="card-body p-0">
                @if ($expenses->isEmpty())
                    <div class="empty-state"><i class="bi bi-arrow-down-circle"></i><p class="mb-0">No expenses recorded.</p></div>
                @else
                    <table class="table mb-0">
                        <tbody>
                            @foreach ($expenses as $row)
                                <tr>
                                    <td>
                                        {{ $row['account']->name }}
                                        <span class="d-block small text-muted">{{ $row['account']->code }}</span>
                                    </td>
                                    <td class="text-end fw-semibold text-danger">{{ currency($row['amount']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td>Total Expenses</td>
                                <td class="text-end text-danger">{{ currency($totalExpenses) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Net Profit / Loss</h6>
                <h4 class="mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ currency($netProfit) }}</h4>
            </div>
        </div>
    </div>
</div>
@endsection