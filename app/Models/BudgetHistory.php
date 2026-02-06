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
        'remaining_budget',
        'total_expenses_count',
        'reset_at',
    ];

    protected function casts(): array
    {
        return [
            'budget_limit' => 'decimal:2',
            'remaining_budget' => 'decimal:2',
            'reset_at' => 'datetime',
        ];
    }
}

