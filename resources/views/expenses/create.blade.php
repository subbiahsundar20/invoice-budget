
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Add Expense</h1>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Back to Expenses</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                @include('expenses.form', ['buttonText' => 'Save Expense'])
            </form>
        </div>
    </div>
</div>
@endsection
