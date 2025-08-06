<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = auth()->user()->transactions()
            ->with(['account', 'category'])
            ->latest('date')
            ->paginate(15);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;
        $types = Transaction::TYPES;
        $recurrenceFrequencies = Transaction::RECURRENCE_FREQUENCIES;

        return view('transactions.create', compact(
            'accounts',
            'categories',
            'types',
            'recurrenceFrequencies'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransaction($request);

        $transaction = auth()->user()->transactions()->create($validated);
        $this->updateAccountBalance($transaction, 'create');

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $accounts = auth()->user()->accounts;
        $categories = auth()->user()->categories;
        $types = Transaction::TYPES;
        $recurrenceFrequencies = Transaction::RECURRENCE_FREQUENCIES;

        return view('transactions.edit', compact(
            'transaction',
            'accounts',
            'categories',
            'types',
            'recurrenceFrequencies'
        ));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        // Store old values for balance adjustment
        $oldAmount = $transaction->amount;
        $oldType = $transaction->type;
        $oldAccountId = $transaction->account_id;

        $validated = $this->validateTransaction($request);
        $transaction->update($validated);

        // Update account balances
        $this->updateAccountBalance($transaction, 'update', [
            'oldAmount' => $oldAmount,
            'oldType' => $oldType,
            'oldAccountId' => $oldAccountId
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $this->updateAccountBalance($transaction, 'delete');
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    protected function validateTransaction(Request $request)
    {
        return $request->validate([
            'account_id' => 'required|exists:accounts,id,user_id,'.auth()->id(),
            'category_id' => 'nullable|exists:categories,id,user_id,'.auth()->id(),
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:'.implode(',', array_keys(Transaction::TYPES)),
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'payee' => 'nullable|string|max:255',
            'is_recurring' => 'boolean',
            'recurrence_frequency' => 'required_if:is_recurring,true|in:'.implode(',', array_keys(Transaction::RECURRENCE_FREQUENCIES)),
            'recurrence_ends_on' => 'nullable|date|after:date'
        ]);
    }

    protected function updateAccountBalance($transaction, $action, $oldValues = [])
    {
        if ($action === 'create') {
            $account = Account::find($transaction->account_id);
            $this->adjustBalance($account, $transaction->type, $transaction->amount);
        } 
        elseif ($action === 'update') {
            // Revert old transaction effect
            $oldAccount = Account::find($oldValues['oldAccountId']);
            $this->adjustBalance($oldAccount, $oldValues['oldType'], -$oldValues['oldAmount']);

            // Apply new transaction effect
            $newAccount = Account::find($transaction->account_id);
            $this->adjustBalance($newAccount, $transaction->type, $transaction->amount);
        } 
        elseif ($action === 'delete') {
            $account = Account::find($transaction->account_id);
            $this->adjustBalance($account, $transaction->type, -$transaction->amount);
        }
    }

    protected function adjustBalance($account, $type, $amount)
    {
        if ($type === 'income') {
            $account->balance += $amount;
        } else {
            $account->balance -= $amount;
        }
        $account->save();
    }
}