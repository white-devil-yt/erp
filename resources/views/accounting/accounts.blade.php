@extends('layouts.app')

@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts')

@section('content')
<div class="page-header">
    <h4><i class="bi bi-journal-richtext me-2 text-primary"></i>Chart of Accounts</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal"><i class="bi bi-plus-lg me-1"></i>New Account</button>
</div>

<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name or code...">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach (\App\Models\Account::TYPES as $key => $label)
                        <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if ($accounts->isEmpty())
            <div class="empty-state"><i class="bi bi-journal"></i><p class="mb-0">No accounts found.</p></div>
        @else
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Account</th>
                            <th>Type</th>
                            <th class="text-end">Balance</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($accounts as $account)
                            <tr>
                                <td class="fw-semibold text-muted">{{ $account->code }}</td>
                                <td>
                                    <a href="{{ route('accounting.ledger', $account) }}" class="fw-semibold text-decoration-none">{{ $account->name }}</a>
                                    <span class="d-block small text-muted">{{ $account->groupLabel() }}</span>
                                </td>
                                <td>
                                    @php
                                        $badge = match ($account->type) {
                                            'asset' => 'bg-soft-primary',
                                            'liability' => 'bg-soft-danger',
                                            'equity' => 'bg-soft-info',
                                            'income' => 'bg-soft-success',
                                            default => 'bg-soft-warning',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $account->typeLabel() }}</span>
                                </td>
                                <td class="text-end fw-semibold">{{ currency($account->normalBalance()) }}</td>
                                <td>
                                    @if ($account->is_active)
                                        <span class="badge bg-soft-success">Active</span>
                                    @else
                                        <span class="badge bg-soft-secondary">Inactive</span>
                                    @endif
                                    @if ($account->is_system)
                                        <span class="badge bg-soft-info ms-1">System</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('accounting.ledger', $account) }}" class="btn btn-sm btn-icon btn-outline-info" title="Ledger"><i class="bi bi-list"></i></a>
                                    @if (!$account->is_system)
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-primary" title="Edit"
                                                data-bs-toggle="modal" data-bs-target="#editAccountModal"
                                                data-id="{{ $account->id }}"
                                                data-name="{{ $account->name }}"
                                                data-description="{{ $account->description }}"
                                                data-active="{{ $account->is_active ? 1 : 0 }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top">{{ $accounts->links() }}</div>
        @endif
    </div>
</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('accounting.accounts.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-plus-circle me-2 text-primary"></i>New Account</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Account Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. 5600" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Account Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Travel Expense" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            @foreach (\App\Models\Account::TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Group <span class="text-danger">*</span></label>
                        <select name="group" class="form-select" required>
                            @foreach (\App\Models\Account::GROUPS as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Account Modal -->
<div class="modal fade" id="editAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="" class="modal-content" id="editAccountForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-pencil me-2 text-primary"></i>Edit Account</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Account Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editAccountName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="editAccountDescription" rows="2" class="form-control"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" id="editAccountActive" class="form-select">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-bs-target="#editAccountModal"]').forEach(btn => {
    btn.addEventListener('click', function () {
        const form = document.getElementById('editAccountForm');
        form.action = '/accounting/accounts/' + this.dataset.id;
        document.getElementById('editAccountName').value = this.dataset.name;
        document.getElementById('editAccountDescription').value = this.dataset.description;
        document.getElementById('editAccountActive').value = this.dataset.active;
    });
});
</script>
@endpush