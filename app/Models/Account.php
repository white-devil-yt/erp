<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'group', 'description', 'is_system', 'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const TYPES = [
        'asset' => 'Asset',
        'liability' => 'Liability',
        'equity' => 'Equity',
        'income' => 'Income',
        'expense' => 'Expense',
    ];

    public const GROUPS = [
        'cash' => 'Cash & Bank',
        'receivable' => 'Accounts Receivable',
        'inventory' => 'Inventory',
        'tax' => 'Taxes',
        'payable' => 'Accounts Payable',
        'liability' => 'Other Liabilities',
        'capital' => 'Capital',
        'revenue' => 'Revenue',
        'cogs' => 'Cost of Goods Sold',
        'expense' => 'Operating Expenses',
        'other' => 'Other',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function debitBalance(): float
    {
        return (float) $this->lines()->sum('debit') - (float) $this->lines()->sum('credit');
    }

    public function creditBalance(): float
    {
        return -$this->debitBalance();
    }

    public function normalBalance(): float
    {
        return in_array($this->type, ['asset', 'expense']) ? $this->debitBalance() : $this->creditBalance();
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function groupLabel(): string
    {
        return self::GROUPS[$this->group] ?? $this->group;
    }
}
