<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $month = $request->query('month');
        $year = $request->query('year');

        $expenses = $this->expenseService->getAllExpenses(
            $status,
            $month ? (int) $month : null,
            $year ? (int) $year : null
        );

        return response()->json($expenses);
    }

    public function show(int $id): JsonResponse
    {
        $expense = Expense::with(['user', 'reviewer'])->findOrFail($id);

        return response()->json($expense);
    }

    public function approve(Request $request, int $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);

        try {
            $expense = $this->expenseService->approveExpense($expense, $request->user());

            return response()->json([
                'message' => 'Expense approved successfully',
                'expense' => $expense->load(['user', 'reviewer']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $expense = Expense::findOrFail($id);

        try {
            $expense = $this->expenseService->rejectExpense(
                $expense,
                $request->user(),
                $validated['reason']
            );

            return response()->json([
                'message' => 'Expense rejected successfully',
                'expense' => $expense->load(['user', 'reviewer']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
