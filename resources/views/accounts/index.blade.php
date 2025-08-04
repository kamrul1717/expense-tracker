@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Accounts</h1>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">Add Account</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Balance</th>
                        <th>Currency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                    <tr>
                        <td>{{ $account->name }}</td>
                        <td>{{ \App\Models\Account::TYPES[$account->type] }}</td>
                        <td class="{{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                            {{ $account->formatted_balance }}
                        </td>
                        <td>{{ strtoupper($account->currency) }}</td>
                        <td>
                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-outline-info">View</a>
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{ $accounts->links() }}
        </div>
    </div>
</div>
@endsection