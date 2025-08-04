<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $currentMonth = now()->format('Y-m');
        
        $totalIncome = $user->transactions()
            ->where('type', 'income')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
            ->sum('amount');
        
        $totalExpenses = $user->transactions()
            ->where('type', 'expense')
            ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
            ->sum('amount');
        
        $recentTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();
        
        $budgets = $user->budgets()
            ->with(['category'])
            ->where(function($query) use ($currentMonth) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->get()
            ->map(function($budget) use ($user, $currentMonth) {
                $spent = $user->transactions()
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                    ->sum('amount');
                
                $budget->spent = $spent;
                $budget->progress = min(100, ($spent / $budget->amount) * 100);
                
                return $budget;
            });
        
        return view('dashboard', compact(
            'totalIncome',
            'totalExpenses',
            'recentTransactions',
            'budgets'
        ));
    }
}
