<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\MonthlyBudget;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function createExpense(User $user, array $data): Expense
    {
        $budget = MonthlyBudget::getOrCreateCurrent();

        $expense = new Expense([
            'user_id' => $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'amount' => $data['amount'],
            'status' => 'pending',
            'budget_month' => $budget->month,
            'budget_year' => $budget->year,
        ]);

        if (!$budget->canApproveAmount((float) $data['amount'])) {
            $expense->status = 'rejected';
            $expense->rejection_reason = 'Expense exceeds remaining monthly budget';
            $expense->reviewed_at = now();
        }

        $expense->save();

        return $expense;
    }

    public function approveExpense(Expense $expense, User $admin): Expense
    {
        if (!$expense->isPending()) {
            throw new \Exception('Only pending expenses can be approved');
        }

        $budget = MonthlyBudget::getOrCreateCurrent();

        if (!$budget->canApproveAmount((float) $expense->amount)) {
            throw new \Exception('Insufficient budget remaining');
        }

        return DB::transaction(function () use ($expense, $admin, $budget) {
            $expense->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $budget->addApprovedAmount((float) $expense->amount);

            return $expense->fresh();
        });
    }

    public function rejectExpense(Expense $expense, User $admin, string $reason): Expense
    {
        if (!$expense->isPending()) {
            throw new \Exception('Only pending expenses can be rejected');
        }

        $expense->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $expense->fresh();
    }

    public function getUserExpenses(User $user, ?string $status = null)
    {
        $query = $user->expenses()->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate(15);
    }

    public function getAllExpenses(?string $status = null, ?int $month = null, ?int $year = null)
    {
        $query = Expense::with(['user', 'reviewer'])->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($month && $year) {
            $query->where('budget_month', $month)->where('budget_year', $year);
        }

        return $query->paginate(15);
    }

    public function getMonthlyStats(?int $month = null, ?int $year = null): array
    {
        if ($month === null || $year === null) {
            $budget = MonthlyBudget::getOrCreateCurrent();
            $month = $budget->month;
            $year = $budget->year;
        } else {
            $budget = MonthlyBudget::getForMonth($month, $year);

            if (!$budget) {
                $budget = MonthlyBudget::getOrCreateCurrent();
            }
        }

        $pendingCount = Expense::where('budget_month', $month)
            ->where('budget_year', $year)
            ->where('status', 'pending')
            ->count();

        $approvedCount = Expense::where('budget_month', $month)
            ->where('budget_year', $year)
            ->where('status', 'approved')
            ->count();

        $rejectedCount = Expense::where('budget_month', $month)
            ->where('budget_year', $year)
            ->where('status', 'rejected')
            ->count();

        return [
            'month' => $month,
            'year' => $year,
            'budget_limit' => (float) $budget->budget_limit,
            'total_approved' => (float) $budget->total_approved,
            'remaining_budget' => $budget->remaining_budget,
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
        ];
    }
}
