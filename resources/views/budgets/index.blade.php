@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Budgets</h1>
        <a href="{{ route('budgets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Budget
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Period</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgets as $budget)
                    <tr>
                        <td>
                            <span class="badge" style="background-color: {{ $budget->category->color }};">
                                {{ $budget->category->name }}
                            </span>
                        </td>
                        <td>{{ number_format($budget->amount, 2) }}</td>
                        <td>{{ \App\Models\Budget::PERIODS[$budget->period] }}</td>
                        <td>
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
                            <small>
                                Spent: {{ number_format($budget->spent, 2) }} / 
                                Budget: {{ number_format($budget->amount, 2) }}
                            </small>
                        </td>
                        <td>
                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $budgets->links() }}
        </div>
    </div>
</div>
@endsection