
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Expense Details</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Expense #{{ $expense->id }}</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-4 mb-3">Description</dt>
                <dd class="col-sm-8 mb-3">{{ $expense->description }}</dd>

                <dt class="col-sm-4 mb-3">Amount</dt>
                <dd class="col-sm-8 mb-3">
                    ₹{{ number_format((float) $expense->amount, 2) }}
                </dd>

                <dt class="col-sm-4 mb-3">Expense Date</dt>
                <dd class="col-sm-8 mb-3">{{ $expense->expense_date->format('d M Y') }}</dd>

                <dt class="col-sm-4 mb-3">Category</dt>
                <dd class="col-sm-8 mb-3">{{ $expense->category ?: '—' }}</dd>

                <dt class="col-sm-4 mb-3">Linked Budget</dt>
                <dd class="col-sm-8 mb-3">{{ $expense->budget?->name ?? 'Unassigned' }}</dd>

                <dt class="col-sm-4 mb-3">Created</dt>
                <dd class="col-sm-8 mb-3">{{ $expense->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
