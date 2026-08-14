@extends('layouts.app')

@section('title', 'CRM - Leads')
@section('page-title', 'CRM - Leads')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person-lines-fill me-2 text-primary"></i>Leads</h4>
    <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>New Lead</a>
</div>

<div class="row g-3 mb-3">
    @foreach ($pipeline as $stage)
        <div class="col-6 col-lg-2">
            <div class="card">
                <div class="card-body py-3 text-center">
                    <span class="d-block text-muted small text-uppercase fw-semibold">{{ $stage['label'] }}</span>
                    <span class="fs-4 fw-bold d-block">{{ $stage['count'] }}</span>
                    <span class="small text-muted">₹{{ number_format($stage['value']) }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name, email, phone or company...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach ($pipeline as $stage)
                        <option value="{{ $stage['key'] }}" {{ request('status') === $stage['key'] ? 'selected' : '' }}>{{ $stage['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Source</label>
                <select name="source" class="form-select">
                    <option value="">All Sources</option>
                    @foreach (\App\Models\Lead::SOURCES as $key => $label)
                        <option value="{{ $key }}" {{ request('source') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                @if ($leads->isEmpty())
                    <div class="empty-state"><i class="bi bi-person-lines-fill"></i><p class="mb-0">No leads found.</p></div>
                @else
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Lead</th>
                                    <th>Source</th>
                                    <th class="text-end">Value</th>
                                    <th>Status</th>
                                    <th>Owner</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leads as $lead)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-info text-white me-2">{{ strtoupper(substr($lead->name, 0, 1)) }}</div>
                                                <div>
                                                    <a href="{{ route('leads.show', $lead) }}" class="fw-semibold text-decoration-none">{{ $lead->name }}</a>
                                                    <span class="d-block small text-muted">{{ $lead->company ?? ($lead->email ?? '—') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="small">{{ $lead->source }}</span></td>
                                        <td class="text-end fw-semibold">₹{{ number_format($lead->value, 2) }}</td>
                                        <td>
                                            @php
                                                $badge = match ($lead->status) {
                                                    'won' => 'bg-soft-success',
                                                    'lost' => 'bg-soft-danger',
                                                    'qualified' => 'bg-soft-info',
                                                    'proposal' => 'bg-soft-warning',
                                                    default => 'bg-soft-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $lead->statusLabel() }}</span>
                                        </td>
                                        <td class="small">{{ $lead->assignee->name ?? '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-icon btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this lead?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 border-top">{{ $leads->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bell me-2 text-primary"></i>Follow-ups Needed</h6>
            </div>
            <div class="card-body p-0">
                @if ($upcomingFollowUps->isEmpty())
                    <div class="empty-state"><i class="bi bi-check2-circle"></i><p class="mb-0">No pending follow-ups.</p></div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($upcomingFollowUps as $lead)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('leads.show', $lead) }}" class="fw-semibold text-decoration-none small">{{ $lead->name }}</a>
                                    <span class="d-block small text-muted">{{ $lead->last_contacted_at->diffForHumans() }}</span>
                                </div>
                                <span class="badge bg-soft-warning">Due</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
