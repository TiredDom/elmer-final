<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected ExpenseService $expenseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $month = $request->query('month');
        $year = $request->query('year');

        $stats = $this->expenseService->getMonthlyStats(
            $month ? (int) $month : null,
            $year ? (int) $year : null
        );

        return response()->json($stats);
    }
}
