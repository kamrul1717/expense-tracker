@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Account Details: {{ $account->name }}</span>
                    <div>
                        <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5>Account Type</h5>
                            <p>{{ \App\Models\Account::TYPES[$account->type] }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Current Balance</h5>
                            <p class="{{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $account->formatted_balance }} {{ strtoupper($account->currency) }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h5>Currency</h5>
                            <p>{{ strtoupper($account->currency) }}</p>
                        </div>
                    </div>

                    <h4 class="mb-3">Recent Transactions</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->date->format('M d, Y') }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        @if($transaction->category)
                                        <span class="badge" style="background-color: {{ $transaction->category->color }};">
                                            {{ $transaction->category->name }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="@if($transaction->type === 'income') text-success @else text-danger @endif">
                                        {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td>{{ ucfirst($transaction->type) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No transactions found for this account</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection