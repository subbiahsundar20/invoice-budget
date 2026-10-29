
@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Expense Tracking</h1>
            <p class="text-muted mb-0">Manage and review your expenses.</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            + Add Expense
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            Please correct the errors and try again.
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-2">
                <div class="col-md-9">
                    <input
                        type="search"
                        name="search"
                        class="form-control"
                        value="{{ $search }}"
                        placeholder="Search description, category or budget..."
                    >
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Search</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Budget</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->expense_date->format('d M Y') }}</td>
                            <td>{{ $expense->category ?: '—' }}</td>
                            <td>{{ $expense->budget?->name ?? 'Unassigned' }}</td>
                            <td class="text-end text-nowrap">
                                ₹{{ number_format((float) $expense->amount, 2) }}
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('expenses.show', $expense) }}"
                                   class="btn btn-sm btn-outline-info">View</a>

                                <a href="{{ route('expenses.edit', $expense) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>

                                <form
                                    action="{{ route('expenses.destroy', $expense) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this expense?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No expenses found. Add an expense to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($expenses->count())
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">Total on this page</th>
                            <th class="text-end">
                                ₹{{ number_format((float) $expenses->sum('amount'), 2) }}
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if ($expenses->hasPages())
            <div class="card-body">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
