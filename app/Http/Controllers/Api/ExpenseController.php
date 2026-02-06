<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $expenses = $this->expenseService->getUserExpenses($request->user(), $status);

        return response()->json($expenses);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $expense = $this->expenseService->createExpense($request->user(), $validated);

        $message = $expense->isRejected()
            ? 'Expense auto-rejected: ' . $expense->rejection_reason
            : 'Expense submitted successfully';

        return response()->json([
            'message' => $message,
            'expense' => $expense,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $expense = $request->user()->expenses()->findOrFail($id);

        return response()->json($expense);
    }
}
