@extends('layouts.app')

@section('title', 'New Journal Entry')
@section('page-title', 'New Journal Entry')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-journal-plus me-2 text-primary"></i>Manual Journal Entry</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('accounting.journal.store') }}" id="journalForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <input type="text" name="description" value="{{ old('description') }}" class="form-control" placeholder="e.g. Paid rent for office" required>
                        </div>
                    </div>

                    <table class="table table-bordered align-middle" id="linesTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width:45%">Account</th>
                                <th style="width:20%">Debit (₹)</th>
                                <th style="width:20%">Credit (₹)</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $oldIds = old('account_ids', [null, null]);
                            @endphp
                            @foreach ($oldIds as $i => $id)
                                <tr>
                                    <td>
                                        <select name="account_ids[]" class="form-select" required>
                                            <option value="">Select account...</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}" {{ old('account_ids.' . $i) == $account->id ? 'selected' : '' }}>{{ $account->code }} · {{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" min="0" name="debits[]" value="{{ old('debits.' . $i) }}" class="form-control debit-input" placeholder="0.00"></td>
                                    <td><input type="number" step="0.01" min="0" name="credits[]" value="{{ old('credits.' . $i) }}" class="form-control credit-input" placeholder="0.00"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-line"><i class="bi bi-x"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-outline-primary" id="addLine"><i class="bi bi-plus-lg me-1"></i>Add Line</button>
                        <div class="text-end small">
                            <span class="me-3">Total Debit: <strong id="totalDebit" class="text-success">0.00</strong></span>
                            <span>Total Credit: <strong id="totalCredit" class="text-danger">0.00</strong></span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Post Entry</button>
                        <a href="{{ route('accounting.journal') }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const accountOptions = `@foreach ($accounts as $account)<option value="{{ $account->id }}">{{ $account->code }} · {{ $account->name }}</option>@endforeach`;

document.getElementById('addLine').addEventListener('click', function () {
    const tbody = document.querySelector('#linesTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td><select name="account_ids[]" class="form-select" required><option value="">Select account...</option>${accountOptions}</select></td>
        <td><input type="number" step="0.01" min="0" name="debits[]" class="form-control debit-input" placeholder="0.00"></td>
        <td><input type="number" step="0.01" min="0" name="credits[]" class="form-control credit-input" placeholder="0.00"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-line"><i class="bi bi-x"></i></button></td>
    `;
    tbody.appendChild(tr);
    bindLineEvents(tr);
});

function bindLineEvents(row) {
    row.querySelector('.remove-line').addEventListener('click', function () {
        const tbody = document.querySelector('#linesTable tbody');
        if (tbody.rows.length > 1) {
            row.remove();
            updateTotals();
        }
    });
    row.querySelectorAll('input').forEach(input => input.addEventListener('input', updateTotals));
}

function updateTotals() {
    let debit = 0, credit = 0;
    document.querySelectorAll('.debit-input').forEach(i => debit += parseFloat(i.value) || 0);
    document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value) || 0);
    document.getElementById('totalDebit').textContent = debit.toFixed(2);
    document.getElementById('totalCredit').textContent = credit.toFixed(2);
}

document.querySelectorAll('#linesTable tbody tr').forEach(bindLineEvents);
updateTotals();

document.getElementById('journalForm').addEventListener('submit', function (e) {
    const d = parseFloat(document.getElementById('totalDebit').textContent);
    const c = parseFloat(document.getElementById('totalCredit').textContent);
    if (Math.abs(d - c) > 0.01) {
        e.preventDefault();
        alert('Total debits must equal total credits.');
    }
});
</script>
@endpush