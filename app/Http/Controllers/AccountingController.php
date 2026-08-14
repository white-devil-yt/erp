<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AccountingController extends Controller
{
    public function dashboard()
    {
        $assets = $this->typeBalance('asset');
        $liabilities = $this->typeBalance('liability');
        $equity = $this->typeBalance('equity');
        $income = $this->typeBalance('income');
        $expenses = $this->typeBalance('expense');

        $cash = $this->accountBalance('1000') + $this->accountBalance('1010');
        $receivable = $this->accountBalance('1100');
        $payable = $this->accountBalance('2000');
        $inventory = $this->accountBalance('1200');

        $recentEntries = JournalEntry::with('user')->latest()->limit(8)->get();

        return view('accounting.dashboard', compact(
            'assets', 'liabilities', 'equity', 'income', 'expenses',
            'cash', 'receivable', 'payable', 'inventory', 'recentEntries'
        ));
    }

    public function accounts(Request $request)
    {
        $query = Account::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $accounts = $query->orderBy('code')->paginate(20)->withQueryString();

        return view('accounting.accounts', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:accounts,code',
            'name' => 'required|string|max:150',
            'type' => 'required|in:'.implode(',', array_keys(Account::TYPES)),
            'group' => 'required|in:'.implode(',', array_keys(Account::GROUPS)),
            'description' => 'nullable|string',
        ]);

        Account::create($data);

        return back()->with('success', 'Account created successfully.');
    }

    public function updateAccount(Request $request, Account $account)
    {
        if ($account->is_system) {
            return back()->with('error', 'System accounts cannot be edited.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $account->update($data);

        return back()->with('success', 'Account updated successfully.');
    }

    public function journal(Request $request)
    {
        $query = JournalEntry::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $entries = $query->latest('date')->paginate(15)->withQueryString();

        return view('accounting.journal', compact('entries'));
    }

    public function journalCreate()
    {
        $accounts = Account::where('is_active', true)->orderBy('code')->get();

        return view('accounting.journal-form', compact('accounts'));
    }

    public function journalStore(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'account_ids' => 'required|array|min:2',
            'account_ids.*' => 'required|exists:accounts,id',
            'debits' => 'required|array|min:2',
            'debits.*' => 'nullable|numeric|min:0',
            'credits' => 'required|array|min:2',
            'credits.*' => 'nullable|numeric|min:0',
        ]);

        $totalDebit = array_sum(array_filter($data['debits'], fn ($v) => $v !== null && $v !== ''));
        $totalCredit = array_sum(array_filter($data['credits'], fn ($v) => $v !== null && $v !== ''));

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', 'Total debits (₹ '.number_format($totalDebit, 2).') must equal total credits (₹ '.number_format($totalCredit, 2).').');
        }

        $entry = JournalEntry::create([
            'date' => $data['date'],
            'type' => 'journal',
            'description' => $data['description'],
            'user_id' => auth()->id(),
        ]);

        foreach ($data['account_ids'] as $i => $accountId) {
            $debit = (float) ($data['debits'][$i] ?? 0);
            $credit = (float) ($data['credits'][$i] ?? 0);
            if ($debit <= 0 && $credit <= 0) {
                continue;
            }
            $entry->lines()->create([
                'account_id' => $accountId,
                'debit' => $debit,
                'credit' => $credit,
            ]);
        }

        return redirect()->route('accounting.journal')->with('success', 'Journal entry #'.$entry->id.' posted successfully.');
    }

    public function journalShow(JournalEntry $entry)
    {
        $entry->load('lines.account', 'user');

        return view('accounting.journal-show', compact('entry'));
    }

    public function trialBalance()
    {
        $lines = JournalEntryLine::with('account')
            ->selectRaw('account_id, SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('account_id')
            ->orderByDesc(Account::select('code')->whereColumn('accounts.id', 'journal_entry_lines.account_id'))
            ->get()
            ->map(function ($line) {
                return [
                    'account' => $line->account,
                    'debit' => (float) $line->total_debit,
                    'credit' => (float) $line->total_credit,
                ];
            });

        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');

        return view('accounting.trial-balance', compact('lines', 'totalDebit', 'totalCredit'));
    }

    public function incomeStatement(Request $request)
    {
        $query = JournalEntryLine::with('account');

        if ($request->filled('from')) {
            $query->whereHas('entry', fn ($q) => $q->whereDate('date', '>=', $request->from));
        }
        if ($request->filled('to')) {
            $query->whereHas('entry', fn ($q) => $q->whereDate('date', '<=', $request->to));
        }

        $lines = $query->get();

        $income = $lines->filter(fn ($l) => $l->account->type === 'income')
            ->groupBy('account_id')
            ->map(fn ($g) => [
                'account' => $g->first()->account,
                'amount' => $g->sum(fn ($l) => $l->credit - $l->debit),
            ])
            ->values();

        $expenses = $lines->filter(fn ($l) => $l->account->type === 'expense')
            ->groupBy('account_id')
            ->map(fn ($g) => [
                'account' => $g->first()->account,
                'amount' => $g->sum(fn ($l) => $l->debit - $l->credit),
            ])
            ->values();

        $totalIncome = $income->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $from = $request->from ?? null;
        $to = $request->to ?? null;

        return view('accounting.income-statement', compact('income', 'expenses', 'totalIncome', 'totalExpenses', 'netProfit', 'from', 'to'));
    }

    public function balanceSheet()
    {
        $assetGroups = $this->groupedBalance('asset');
        $liabilityGroups = $this->groupedBalance('liability');
        $equityGroups = $this->groupedBalance('equity');

        $totalAssets = $assetGroups->sum('balance');
        $totalLiabilities = $liabilityGroups->sum('balance');
        $totalEquity = $equityGroups->sum('balance');

        return view('accounting.balance-sheet', compact(
            'assetGroups', 'liabilityGroups', 'equityGroups',
            'totalAssets', 'totalLiabilities', 'totalEquity'
        ));
    }

    public function ledger(Request $request, Account $account)
    {
        $query = $account->lines()->with('entry');

        if ($request->filled('from')) {
            $query->whereHas('entry', fn ($q) => $q->whereDate('date', '>=', $request->from));
        }
        if ($request->filled('to')) {
            $query->whereHas('entry', fn ($q) => $q->whereDate('date', '<=', $request->to));
        }

        $lines = $query->orderByDesc('id')->paginate(25)->withQueryString();

        $opening = 0.0;
        if ($request->filled('from')) {
            $opening = (float) $account->lines()
                ->whereHas('entry', fn ($q) => $q->whereDate('date', '<', $request->from))
                ->get()
                ->sum(fn ($l) => $l->debit - $l->credit);
        }

        return view('accounting.ledger', compact('account', 'lines', 'opening'));
    }

    private function accountBalance(string $code): float
    {
        $account = Account::where('code', $code)->first();

        return $account ? $account->normalBalance() : 0;
    }

    private function typeBalance(string $type): float
    {
        $total = 0;
        foreach (Account::where('type', $type)->get() as $account) {
            $total += $account->normalBalance();
        }

        return $total;
    }

    private function groupedBalance(string $type): Collection
    {
        return Account::where('type', $type)->get()
            ->groupBy('group')
            ->map(fn ($accounts) => [
                'group' => $accounts->first()->groupLabel(),
                'balance' => $accounts->sum(fn ($a) => $a->normalBalance()),
            ])
            ->values();
    }
}
