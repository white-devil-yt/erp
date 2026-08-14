<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'company', 'source', 'status', 'value',
        'expected_close_date', 'last_contacted_at', 'assigned_to',
        'customer_id', 'notes',
    ];

    protected $casts = [
        'value' => 'float',
        'expected_close_date' => 'date',
        'last_contacted_at' => 'date',
    ];

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'proposal' => 'Proposal',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    public const SOURCES = [
        'website' => 'Website',
        'referral' => 'Referral',
        'walk-in' => 'Walk-in',
        'social-media' => 'Social Media',
        'cold-call' => 'Cold Call',
        'advertisement' => 'Advertisement',
        'other' => 'Other',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    public function isWon(): bool
    {
        return $this->status === 'won';
    }

    public function isLost(): bool
    {
        return $this->status === 'lost';
    }

    public function isConverted(): bool
    {
        return $this->isWon() && $this->customer_id !== null;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
