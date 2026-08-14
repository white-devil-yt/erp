@extends('layouts.app')

@section('title', $lead->name)
@section('page-title', $lead->name)

@section('content')
<div class="page-header">
    <h4><i class="bi bi-person-lines-fill me-2 text-primary"></i>{{ $lead->name }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        @if ($lead->isWon() && !$lead->isConverted())
            <form action="{{ route('leads.convert', $lead) }}" method="POST" onsubmit="return confirm('Convert this lead into a customer?');">
                @csrf
                <button type="submit" class="btn btn-success"><i class="bi bi-arrow-right-circle me-1"></i>Convert to Customer</button>
            </form>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Lead Details</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5">Status</dt>
                    <dd class="col-7">
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
                    </dd>
                    <dt class="col-5">Source</dt>
                    <dd class="col-7">{{ $lead->source }}</dd>
                    <dt class="col-5">Value</dt>
                    <dd class="col-7 fw-semibold">₹{{ number_format($lead->value, 2) }}</dd>
                    <dt class="col-5">Company</dt>
                    <dd class="col-7">{{ $lead->company ?? '—' }}</dd>
                    <dt class="col-5">Email</dt>
                    <dd class="col-7 text-break">{{ $lead->email ?? '—' }}</dd>
                    <dt class="col-5">Phone</dt>
                    <dd class="col-7">{{ $lead->phone ?? '—' }}</dd>
                    <dt class="col-5">Owner</dt>
                    <dd class="col-7">{{ $lead->assignee->name ?? 'Unassigned' }}</dd>
                    <dt class="col-5">Expected Close</dt>
                    <dd class="col-7">{{ $lead->expected_close_date?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-5">Last Contacted</dt>
                    <dd class="col-7">{{ $lead->last_contacted_at?->format('d M Y') ?? 'Never' }}</dd>
                    @if ($lead->isConverted())
                        <dt class="col-5">Customer</dt>
                        <dd class="col-7">
                            <a href="{{ route('customers.show', $lead->customer) }}" class="text-decoration-none">{{ $lead->customer->name }}</a>
                        </dd>
                    @endif
                </dl>
                @if ($lead->notes)
                    <hr>
                    <div class="small text-muted">{{ $lead->notes }}</div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Update Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('leads.status', $lead) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <select name="status" class="form-select">
                        @foreach (\App\Models\Lead::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ $lead->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary flex-shrink-0">Move</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-journal-plus me-2 text-primary"></i>Log Activity</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('leads.activities', $lead) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                @foreach (\App\Models\LeadActivity::TYPES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="next_follow_up" class="form-control" title="Next follow-up date">
                        </div>
                        <div class="col-md-5">
                            <input type="text" name="note" class="form-control" placeholder="What happened?" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-plus-lg me-1"></i>Add Activity</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Activity History</h6>
            </div>
            <div class="card-body p-0">
                @if ($lead->activities->isEmpty())
                    <div class="empty-state"><i class="bi bi-journal-text"></i><p class="mb-0">No activities yet.</p></div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($lead->activities as $activity)
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-soft-primary mb-1">{{ $activity->type }}</span>
                                        <div class="small">{{ $activity->note }}</div>
                                        <span class="small text-muted">
                                            {{ $activity->user->name ?? 'System' }} · {{ $activity->created_at->diffForHumans() }}
                                            @if ($activity->next_follow_up)
                                                · Follow up by {{ $activity->next_follow_up->format('d M Y') }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection