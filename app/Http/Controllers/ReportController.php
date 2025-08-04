<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        $defaultStart = now()->startOfMonth();
        $defaultEnd = now()->endOfMonth();

        // Get filter values from request or use defaults
        $startDate = request('start_date', $defaultStart->format('Y-m-d'));
        $endDate = request('end_date', $defaultEnd->format('Y-m-d'));
        $type = request('type', 'all');
        $categoryId = request('category_id');

        // Convert to Carbon instances for querying
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Base query
        $query = $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate]);

        // Apply filters
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Get transactions for the report
        $transactions = $query->orderBy('date', 'desc')->get();

        // Calculate summary statistics
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netAmount = $totalIncome - $totalExpenses;

        // Get categories for filter dropdown
        $categories = $user->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('reports.index', compact(
            'transactions',
            'totalIncome',
            'totalExpenses',
            'netAmount',
            'categories',
            'startDate',
            'endDate',
            'type',
            'categoryId'
        ));
    }

    public function spendingByCategory(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $data = auth()->user()->transactions()
            ->selectRaw('category_id, SUM(amount) as total')
            ->with(['category'])
            ->where('type', 'expense')
            ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->groupBy('category_id')
            ->get();
        
        return response()->json([
            'labels' => $data->pluck('category.name'),
            'data' => $data->pluck('total')
        ]);
    }
}
