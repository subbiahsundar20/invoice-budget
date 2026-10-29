@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Budget Details</h2>
            <p class="text-muted mb-0">{{ $budget->name }}</p>
        </div>

        <div>
            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
                Back
            </a>

            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-warning">
                Edit Budget
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Budget Information</div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <strong>Budget Name</strong>
                    <div>{{ $budget->name }}</div>
                </div>

                <div class="col-md-6 mb-4">
                    <strong>Category</strong>
                    <div>{{ $budget->category ?: 'Not specified' }}</div>
                </div>

                <div class="col-md-6 mb-4">
                    <strong>Budget Amount</strong>
                    <h4 class="text-primary">
                        ₹{{ number_format((float) $budget->amount, 2) }}
                    </h4>
                </div>

                <div class="col-md-6 mb-4">
                    <strong>Period</strong>
                    <div>{{ ucfirst($budget->period) }}</div>
                </div>

                <div class="col-md-6 mb-4">
                    <strong>Start Date</strong>
                    <div>{{ $budget->start_date->format('d M Y') }}</div>
                </div>

                <div class="col-md-6 mb-4">
                    <strong>End Date</strong>
                    <div>{{ $budget->end_date->format('d M Y') }}</div>
                </div>

                <div class="col-md-6">
                    <strong>Created</strong>
                    <div>{{ $budget->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection