<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'budget_limit',
        'total_approved',
    ];

    protected function casts(): array
    {
        return [
            'budget_limit' => 'decimal:2',
            'total_approved' => 'decimal:2',
        ];
    }

    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->budget_limit - (float) $this->total_approved;
    }

    public static function getOrCreateCurrent(): self
    {
        $month = (int) now()->format('n');
        $year = (int) now()->format('Y');

        return self::firstOrCreate(
            ['month' => $month, 'year' => $year],
            ['budget_limit' => 10000.00, 'total_approved' => 0.00]
        );
    }

    public static function getForMonth(int $month, int $year): ?self
    {
        return self::where('month', $month)->where('year', $year)->first();
    }

    public function canApproveAmount(float $amount): bool
    {
        return $this->remaining_budget >= $amount;
    }

    public function addApprovedAmount(float $amount): void
    {
        $this->increment('total_approved', $amount);
    }
}
