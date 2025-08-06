@extends('layouts.app')



@section('content')
<div class="container py-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Report Filters -->
        <div class="p-6 border-b">
            <form method="GET" action="{{ route('reports.index') }}" id="report-filters" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               id="start_date" name="start_date" 
                               value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               id="end_date" name="end_date" 
                               value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>All Transactions</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expenses</option>
                        </select>
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        <i class="fas fa-filter mr-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <i class="fas fa-sync-alt mr-2"></i> Reset
                    </a>
                    <button type="button" onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <a href="{{ route('reports.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                        <i class="fas fa-file-export mr-2"></i> Export
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="fas fa-money-bill-wave fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-blue-800">Total Income</h3>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($totalIncome, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-red-800">Total Expenses</h3>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($totalExpenses, 2) }}</p>
                        </div>
                    </div>
                </div>
                <div class="{{ $netAmount >= 0 ? 'bg-green-50 border-green-100' : 'bg-yellow-50 border-yellow-100' }} p-4 rounded-lg border">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $netAmount >= 0 ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} mr-4">
                            <i class="fas fa-balance-scale fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium {{ $netAmount >= 0 ? 'text-green-800' : 'text-yellow-800' }}">Net Balance</h3>
                            <p class="text-2xl font-bold {{ $netAmount >= 0 ? 'text-green-600' : 'text-yellow-600' }}">{{ number_format($netAmount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="p-6 border-b">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-blue-500"></i> Spending by Category
                    </h3>
                    <canvas id="categoryChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i> Hover over chart segments for details
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <h3 class="text-lg font-medium mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-green-500"></i> Income vs Expenses
                    </h3>
                    <canvas id="incomeExpenseChart" height="250"></canvas>
                    <div class="mt-4 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i> Comparison of total income and expenses
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="p-6">
            <h3 class="text-lg font-medium mb-4 flex items-center">
                <i class="fas fa-list-alt mr-2 text-gray-500"></i> Transaction Details
                <span class="ml-auto text-sm font-normal text-gray-500">
                    Showing {{ $transactions->count() }} transactions
                </span>
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $transaction->description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($transaction->category)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" style="background-color: {{ $transaction->category->color }}; color: white;">
                                    {{ $transaction->category->name }}
                                </span>
                                @else
                                <span class="text-gray-400">Uncategorized</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $transaction->type === 'income' ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                <i class="fas fa-exclamation-circle mr-1"></i> No transactions found for the selected filters
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->count() > 0)
            <div class="mt-4 text-sm text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-1"></i> Showing transactions from 
                {{ Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Spending by Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        
        fetch(`{{ route('reports.spending-by-category') }}?${new URLSearchParams({
            start_date: document.getElementById('start_date').value,
            end_date: document.getElementById('end_date').value,
            type: document.getElementById('type').value,
            category_id: document.getElementById('category_id').value
        })}`)
            .then(response => response.json())
            .then(data => {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
                                '#9966FF', '#FF9F40', '#8AC24A', '#607D8B',
                                '#E91E63', '#9C27B0'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ${percentage}% (${value.toFixed(2)})`;
                                    }
                                }
                            }
                        }
                    }
                });
            });

        // Income vs Expense Chart
        const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
        
        fetch(`{{ route('reports.income-vs-expense') }}?${new URLSearchParams({
            start_date: document.getElementById('start_date').value,
            end_date: document.getElementById('end_date').value,
            type: document.getElementById('type').value,
            category_id: document.getElementById('category_id').value
        })}`)
            .then(response => response.json())
            .then(data => {
                new Chart(incomeExpenseCtx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Amount',
                            data: data.data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 99, 132, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toFixed(2);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.dataset.label}: ${context.raw.toFixed(2)}`;
                                    }
                                }
                            }
                        }
                    }
                });
            });

        // Update charts when filters change
        document.getElementById('report-filters').addEventListener('change', function() {
            // You could add logic here to refresh the charts without page reload
            // using the same fetch calls above
        });
    });
</script>
@endpush

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .bg-white, .bg-white * {
            visibility: visible;
        }
        .bg-white {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
            border: none;
        }
        .no-print, .no-print * {
            display: none !important;
        }
        .break-after {
            page-break-after: always;
        }
    }
</style>
@endpush