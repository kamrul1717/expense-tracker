@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Transactions</h1>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Transaction
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->date->format('M d, Y') }}</td>
                            <td>{{ $transaction->account->name }}</td>
                            <td>{{ $transaction->description }}</td>
                            <td>
                                @if($transaction->category)
                                <span class="badge" style="background-color: {{ $transaction->category->color }};">
                                    {{ $transaction->category->name }}
                                </span>
                                @endif
                            </td>
                            <td class="@if($transaction->type === 'income') text-success @else text-danger @endif">
                                {{ $transaction->formatted_amount }}
                            </td>
                            <td>{!! $transaction->type_badge !!}</td>
                            <td>
                                <a href="{{ route('transactions.show', $transaction) }}" class="btn btn-sm btn-outline-info">View</a>
                                <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection