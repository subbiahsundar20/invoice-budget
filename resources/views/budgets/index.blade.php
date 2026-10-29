@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Budget Management</h2>
            <p class="text-muted mb-0">Manage your monthly and yearly budgets.</p>
        </div>

        <a href="{{ route('budgets.create') }}" class="btn btn-primary">
            + Create Budget
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('budgets.index') }}" class="mb-3">
        <div class="input-group">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search name, category, or period..."
                value="{{ $search }}"
            >

            <button type="submit" class="btn btn-outline-primary">
                Search
            </button>

            <a href="{{ route('budgets.index') }}"
               class="btn btn-outline-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Period</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th style="min-width: 180px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($budgets as $budget)
                        <tr>
                            <td>{{ $budget->name }}</td>
                            <td>{{ $budget->category ?: '—' }}</td>
                            <td>₹{{ number_format((float) $budget->amount, 2) }}</td>
                            <td>{{ ucfirst($budget->period) }}</td>
                            <td>{{ $budget->start_date->format('d M Y') }}</td>
                            <td>{{ $budget->end_date->format('d M Y') }}</td>

                            <td>
                                <a href="{{ route('budgets.show', $budget) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a>

                                <a href="{{ route('budgets.edit', $budget) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('budgets.destroy', $budget) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this budget?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No budgets found. Create your first budget to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $budgets->links() }}
    </div>
</div>
@endsection