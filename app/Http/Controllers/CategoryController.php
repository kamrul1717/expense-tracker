<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = auth()->user()->categories()
            ->latest()
            ->paginate(10);
            
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create', [
            'types' => Category::TYPES
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,'.auth()->id(),
            'type' => 'required|in:expense,income',
            'color' => 'required|string|max:20',
            'icon' => 'nullable|string|max:50',
        ]);

        auth()->user()->categories()->create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);
        
        $transactions = $category->transactions()
            ->with('account')
            ->latest()
            ->paginate(10);
            
        return view('categories.show', compact('category', 'transactions'));
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', [
            'category' => $category,
            'types' => Category::TYPES
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id.',id,user_id,'.auth()->id(),
            'type' => 'required|in:expense,income',
            'color' => 'required|string|max:20',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        if ($category->transactions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete category with transactions.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}