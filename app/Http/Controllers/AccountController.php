<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = auth()->user()->accounts()->latest()->paginate(10);
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $accountTypes = Account::TYPES;
        return view('accounts.create', compact('accountTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Account::TYPES)),
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
        ]);

        $validated['user_id'] = auth()->id();

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);
        
        $transactions = $account->transactions()
            ->with('category')
            ->latest()
            ->paginate(10);
            
        return view('accounts.show', compact('account', 'transactions'));
    }

    public function edit(Account $account)
    {
        $this->authorize('update', $account);
        $accountTypes = Account::TYPES;
        return view('accounts.edit', compact('account', 'accountTypes'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorize('update', $account);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Account::TYPES)),
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        
        if ($account->transactions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete account with transactions. Delete transactions first.');
        }
        
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}