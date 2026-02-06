<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetHistory;
use App\Models\MonthlyBudget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function getCurrentBudget(): JsonResponse
    {
        $budget = MonthlyBudget::getOrCreateCurrent();

        return response()->json([
            'month' => $budget->month,
            'year' => $budget->year,
            'budget_limit' => (float) $budget->budget_limit,
            'total_approved' => (float) $budget->total_approved,
            'remaining_budget' => $budget->remaining_budget,
        ]);
    }

    public function advanceMonth(Request $request): JsonResponse
    {
        $budget = MonthlyBudget::getOrCreateCurrent();

        try {
            $result = $budget->advanceToNextMonth();

            return response()->json([
                'message' => 'Budget advanced to next month successfully',
                'history' => $result['history'],
                'new_budget' => [
                    'month' => $result['new_month'],
                    'year' => $result['new_year'],
                    'budget_limit' => (float) $budget->budget_limit,
                    'total_approved' => (float) $budget->total_approved,
                    'remaining_budget' => $budget->remaining_budget,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to advance month',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getHistory(Request $request): JsonResponse
    {
        $histories = BudgetHistory::orderBy('reset_at', 'desc')
            ->paginate(15);

        return response()->json($histories);
    }
}

