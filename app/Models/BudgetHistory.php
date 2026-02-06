<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'budget_limit',
        'total_approved',
        'remaining_budget',
        'total_expenses_count',
        'approved_count',
        'rejected_count',
        'reset_at',
    ];

    protected function casts(): array
    {
        return [
            'budget_limit' => 'decimal:2',
            'total_approved' => 'decimal:2',
            'remaining_budget' => 'decimal:2',
            'reset_at' => 'datetime',
        ];
    }
}

