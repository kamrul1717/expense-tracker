@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Category: {{ $category->name }}</span>
                    <div>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h5>Type</h5>
                            <p>{{ $category->type_name }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5>Color</h5>
                            <span class="badge" style="background-color: {{ $category->color }}; color: white">
                                {{ $category->color }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <h5>Icon</h5>
                            <p>
                                @if($category->icon)
                                <i class="{{ $category->icon }} fa-2x"></i>
                                @else
                                None
                                @endif
                            </p>
                        </div>
                    </div>

                    <h4>Transactions</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->date->format('M d, Y') }}</td>
                                    <td>{{ $transaction->account->name }}</td>
                                    <td class="{{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No transactions found for this category</td>
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