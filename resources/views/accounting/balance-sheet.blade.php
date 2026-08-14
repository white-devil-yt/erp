@extends('layouts.app')

@section('title', 'Balance Sheet')
@section('page-title', 'Balance Sheet')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Balance Sheet</h4>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-primary"><i class="bi bi-bank me-2"></i>Assets</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        @foreach ($assetGroups as $group)
                            <tr>
                                <td>{{ $group['group'] }}</td>
                                <td class="text-end fw-semibold">{{ currency($group['balance']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td>Total Assets</td>
                            <td class="text-end text-primary">{{ currency($totalAssets) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0 text-danger"><i class="bi bi-receipt me-2"></i>Liabilities</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        @foreach ($liabilityGroups as $group)
                            <tr>
                                <td>{{ $group['group'] }}</td>
                                <td class="text-end fw-semibold">{{ currency($group['balance']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td>Total Liabilities</td>
                            <td class="text-end text-danger">{{ currency($totalLiabilities) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 text-info"><i class="bi bi-pie-chart me-2"></i>Equity</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        @foreach ($equityGroups as $group)
                            <tr>
                                <td>{{ $group['group'] }}</td>
                                <td class="text-end fw-semibold">{{ currency($group['balance']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td>Total Equity</td>
                            <td class="text-end text-info">{{ currency($totalEquity) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Total Liabilities + Equity</h6>
                <h4 class="mb-0 {{ abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01 ? 'text-success' : 'text-danger' }}">
                    {{ currency($totalLiabilities + $totalEquity) }}
                </h4>
            </div>
        </div>
    </div>
</div>
@endsection