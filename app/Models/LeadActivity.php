<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id', 'user_id', 'type', 'note', 'next_follow_up',
    ];

    protected $casts = [
        'next_follow_up' => 'date',
    ];

    public const TYPES = [
        'call' => 'Call',
        'email' => 'Email',
        'meeting' => 'Meeting',
        'note' => 'Note',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
