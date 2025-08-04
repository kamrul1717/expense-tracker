<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = auth()->user()->budgets()
            ->with('category')
            ->where(function($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->latest()
            ->paginate(10);

        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = auth()->user()->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('budgets.create', [
            'categories' => $categories,
            'periods' => Budget::PERIODS
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id,user_id,'.auth()->id(),
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:' . implode(',', array_keys(Budget::PERIODS)),
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $validated['user_id'] = auth()->id();

        Budget::create($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);

        $categories = auth()->user()->categories()
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        return view('budgets.edit', [
            'budget' => $budget,
            'categories' => $categories,
            'periods' => Budget::PERIODS
        ]);
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id,user_id,'.auth()->id(),
            'amount' => 'required|numeric|min:0.01',
            'period' => 'required|in:' . implode(',', array_keys(Budget::PERIODS)),
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}