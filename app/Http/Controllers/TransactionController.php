<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        
        return view('transactions.create', [
            'accounts' => $user->accounts,
            'categories' => $user->categories,
            'types' => ['expense', 'income', 'transfer']
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id,user_id,'.auth()->id(),
            'category_id' => 'nullable|exists:categories,id,user_id,'.auth()->id(),
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:expense,income,transfer',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'payee' => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
            'recurrence_frequency' => 'required_if:is_recurring,true|in:daily,weekly,monthly,yearly',
            'recurrence_ends_on' => 'nullable|date|after:date',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048'
        ]);
        
        $transaction = auth()->user()->transactions()->create($validated);
        
        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments');
                
                $transaction->attachments()->create([
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
            }
        }
        
        // Update account balance
        $account = Account::find($validated['account_id']);
        
        if ($validated['type'] === 'income') {
            $account->balance += $validated['amount'];
        } elseif ($validated['type'] === 'expense') {
            $account->balance -= $validated['amount'];
        }
        
        $account->save();
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction added successfully.');
    }
}
