@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Transaction Details</span>
                    <div>
                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Date</h5>
                            <p>{{ $transaction->date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Type</h5>
                            <p>{!! $transaction->type_badge !!}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Account</h5>
                            <p>{{ $transaction->account->name }} ({{ $transaction->account->type }})</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Category</h5>
                            <p>
                                @if($transaction->category)
                                <span class="badge" style="background-color: {{ $transaction->category->color }};">
                                    {{ $transaction->category->name }}
                                </span>
                                @else
                                N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Amount</h5>
                            <p class="@if($transaction->type === 'income') text-success @else text-danger @endif">
                                {{ $transaction->formatted_amount }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Payee</h5>
                            <p>{{ $transaction->payee ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($transaction->is_recurring)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Recurrence</h5>
                            <p>{{ Transaction::RECURRENCE_FREQUENCIES[$transaction->recurrence_frequency] }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Ends On</h5>
                            <p>{{ $transaction->recurrence_ends_on ? $transaction->recurrence_ends_on->format('M d, Y') : 'Never' }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <h5>Description</h5>
                        <p>{{ $transaction->description ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection