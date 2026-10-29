
@extends('layouts.app')

@section('title', 'Suppliers')
@section('heading', 'Suppliers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5>Supplier Management</h5>
        <p class="text-muted">Manage your suppliers.</p>
    </div>

    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
        + Add Supplier
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('suppliers.index') }}" class="mb-3">
            <div class="input-group">
                <input name="search" class="form-control"
                       placeholder="Search suppliers..."
                       value="{{ request('search') }}">
                <button class="btn btn-dark">Search</button>
                <a href="{{ route('suppliers.index') }}"
                   class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->company_name ?? '-' }}</td>
                            <td>{{ $supplier->email ?? '-' }}</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>
                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>

                                <form method="POST"
                                      action="{{ route('suppliers.destroy', $supplier) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No suppliers found. Add your first supplier.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $suppliers->links() }}
    </div>
</div>
@endsection
