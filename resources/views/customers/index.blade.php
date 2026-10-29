
@extends('layouts.app')

@section('title', 'Customers')
@section('heading', 'Customers')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Customer Management</h5>
            <p class="text-muted mb-0">Manage your customers and their details.</p>
        </div>

        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            + Add Customer
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form method="GET" action="{{ route('customers.index') }}"
                  class="mb-3">
                <div class="input-group">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search customers by name, email or company"
                           value="{{ request('search') }}">

                    <button class="btn btn-dark" type="submit">Search</button>

                    <a href="{{ route('customers.index') }}"
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
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customers->firstItem() + $loop->index }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->company_name ?? '-' }}</td>
                                <td>{{ $customer->email ?? '-' }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('customers.destroy', $customer) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this customer?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger"
                                                type="submit">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No customers found. Click "Add Customer" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
