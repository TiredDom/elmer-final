<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        // Get the most recent budget record (by month/year combination)
        $latest = self::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        // If no budget exists, create one for the actual current month
        if (!$latest) {
            $month = (int) now()->format('n');
            $year = (int) now()->format('Y');

            return self::create([
                'month' => $month,
                'year' => $year,
                'budget_limit' => 10000.00,
                'total_approved' => 0.00,
            ]);
        }

        // Return the latest budget (which represents the "current" virtual month)
        return $latest;
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

    public function advanceToNextMonth(): array
    {
        return DB::transaction(function () {
            // Get expense counts for this budget period
            $totalExpenses = Expense::where('budget_month', $this->month)
                ->where('budget_year', $this->year)
                ->count();

            $approvedCount = Expense::where('budget_month', $this->month)
                ->where('budget_year', $this->year)
                ->where('status', 'approved')
                ->count();

            $rejectedCount = Expense::where('budget_month', $this->month)
                ->where('budget_year', $this->year)
                ->where('status', 'rejected')
                ->count();

            // Save current month to history
            $history = BudgetHistory::create([
                'month' => $this->month,
                'year' => $this->year,
                'budget_limit' => $this->budget_limit,
                'total_approved' => $this->total_approved,
                'remaining_budget' => $this->remaining_budget,
                'total_expenses_count' => $totalExpenses,
                'approved_count' => $approvedCount,
                'rejected_count' => $rejectedCount,
                'reset_at' => now(),
            ]);

            // Calculate next month
            $nextMonth = $this->month + 1;
            $nextYear = $this->year;

            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }

            // Check if next month budget already exists
            $nextBudget = self::where('month', $nextMonth)
                ->where('year', $nextYear)
                ->first();

            if (!$nextBudget) {
                // Create new budget record for next month
                $nextBudget = self::create([
                    'month' => $nextMonth,
                    'year' => $nextYear,
                    'budget_limit' => $this->budget_limit,
                    'total_approved' => 0.00,
                ]);
            }

            return [
                'history' => $history,
                'new_month' => $nextMonth,
                'new_year' => $nextYear,
            ];
        });
    }
}
