<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    protected $fillable = [
        'employee_id', 'month', 'present_days', 'total_working_days', 'basic_salary',
        'allowances', 'deductions', 'overtime_amount', 'bonus', 'net_salary',
        'status', 'paid_date',
    ];

    protected $casts = [
        'paid_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}