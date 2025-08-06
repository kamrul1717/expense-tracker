@php
    $isEdit = isset($transaction);
    $route = $isEdit ? route('transactions.update', $transaction) : route('transactions.store');
    $method = $isEdit ? 'PUT' : 'POST';
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ $isEdit ? 'Edit Transaction' : 'Create Transaction' }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ $route }}">
                        @csrf
                        @method($method)

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="account_id" class="form-label">Account</label>
                                <select id="account_id" name="account_id" class="form-select @error('account_id') is-invalid @enderror" required>
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" 
                                            {{ old('account_id', $transaction->account_id ?? '') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} ({{ $account->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category</label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $transaction->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type</label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    @foreach($types as $key => $type)
                                        <option value="{{ $key }}" 
                                            {{ old('type', $transaction->type ?? '') == $key ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                    id="amount" name="amount" value="{{ old('amount', $transaction->amount ?? '') }}" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                    id="date" name="date" value="{{ old('date', isset($transaction) ? $transaction->date->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="payee" class="form-label">Payee</label>
                                <input type="text" class="form-control @error('payee') is-invalid @enderror" 
                                    id="payee" name="payee" value="{{ old('payee', $transaction->payee ?? '') }}">
                                @error('payee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description">{{ old('description', $transaction->description ?? '') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" 
                                        value="1" {{ old('is_recurring', $transaction->is_recurring ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_recurring">
                                        Recurring Transaction
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="recurrenceFields" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="recurrence_frequency" class="form-label">Frequency</label>
                                    <select id="recurrence_frequency" name="recurrence_frequency" class="form-select">
                                        @foreach($recurrenceFrequencies as $key => $frequency)
                                            <option value="{{ $key }}" 
                                                {{ old('recurrence_frequency', $transaction->recurrence_frequency ?? '') == $key ? 'selected' : '' }}>
                                                {{ $frequency }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="recurrence_ends_on" class="form-label">Ends On</label>
                                    <input type="date" class="form-control" 
                                        id="recurrence_ends_on" name="recurrence_ends_on" 
                                        value="{{ old('recurrence_ends_on', isset($transaction) ? optional($transaction->recurrence_ends_on)->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ $isEdit ? 'Update' : 'Create' }} Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isRecurringCheckbox = document.getElementById('is_recurring');
        const recurrenceFields = document.getElementById('recurrenceFields');

        function toggleRecurrenceFields() {
            recurrenceFields.style.display = isRecurringCheckbox.checked ? 'block' : 'none';
        }

        isRecurringCheckbox.addEventListener('change', toggleRecurrenceFields);
        toggleRecurrenceFields(); // Initialize on page load
    });
</script>
@endsection