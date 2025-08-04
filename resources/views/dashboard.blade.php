@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card text-white bg-primary">
                                <div class="card-body">
                                    <h5 class="card-title">Total Income</h5>
                                    <h3 class="card-text">{{ format_currency($totalIncome) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-danger">
                                <div class="card-body">
                                    <h5 class="card-title">Total Expenses</h5>
                                    <h3 class="card-text">{{ format_currency($totalExpenses) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-white bg-success">
                                <div class="card-body">
                                    <h5 class="card-title">Net Balance</h5>
                                    <h3 class="card-text">{{ format_currency($totalIncome - $totalExpenses) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Budget Progress -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">Budget Progress</div>
                                <div class="card-body">
                                    @foreach($budgets as $budget)
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $budget->category->name }}</span>
                                            <span>{{ format_currency($budget->spent) }} / {{ format_currency($budget->amount) }}</span>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar 
                                                @if($budget->progress > 90) bg-danger
                                                @elseif($budget->progress > 70) bg-warning
                                                @else bg-success @endif" 
                                                role="progressbar" 
                                                style="width: {{ $budget->progress }}%" 
                                                aria-valuenow="{{ $budget->progress }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ round($budget->progress) }}%
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Recent Transactions</span>
                                    <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">Add Transaction</a>
                                </div>
                                <div class="card-body">
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
                                            @foreach($recentTransactions as $transaction)
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
                                                    {{ format_currency($transaction->amount) }}
                                                </td>
                                                <td>{{ ucfirst($transaction->type) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('transactions.index') }}" class="btn btn-link">View All Transactions</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection