<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
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

    public function spendingByCategory()
    {
        $user = auth()->user();
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        $data = $user->transactions()
            ->with('category')
            ->where('type', 'expense')
            ->whereBetween('date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->get()
            ->groupBy('category.name')
            ->map(function ($transactions) {
                return $transactions->sum('amount');
            })
            ->sortDesc();

        return response()->json([
            'labels' => $data->keys(),
            'data' => $data->values()
        ]);
    }

    public function incomeVsExpense()
    {
        $user = auth()->user();
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        $transactions = $user->transactions()
            ->whereBetween('date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        return response()->json([
            'labels' => ['Income', 'Expenses'],
            'data' => [$income, $expense]
        ]);
    }

    public function export(Request $request)
    {
        $data = $this->getReportData($request);
        
        $fileName = 'financial-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($data) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Date', 'Description', 'Category', 'Amount', 'Type']);
            
            // Add data
            foreach ($data['transactions'] as $transaction) {
                fputcsv($file, [
                    $transaction->date->format('Y-m-d'),
                    $transaction->description,
                    $transaction->category ? $transaction->category->name : '',
                    $transaction->amount,
                    ucfirst($transaction->type)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getReportData(Request $request)
    {
        $user = auth()->user();
        
        // Get filter values from request or use defaults
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $type = $request->input('type', 'all');
        $categoryId = $request->input('category_id');

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

        return [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netAmount' => $netAmount,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => $type,
            'categoryId' => $categoryId
        ];
    }
    
}