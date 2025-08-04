<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = auth()->user()->budgets()
            ->with(['category'])
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->get()
            ->map(function($budget) {
                $spent = auth()->user()->transactions()
                    ->where('category_id', $budget->category_id)
                    ->where('type', 'expense')
                    ->whereBetween('date', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])
                    ->sum('amount');
                
                $budget->spent = $spent;
                $budget->progress = min(100, ($spent / $budget->amount) * 100);
                $budget->remaining = max(0, $budget->amount - $spent);
                
                return $budget;
            });
        
        return view('budgets.index', compact('budgets'));
    }
}
