@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Products & Services</h2>
            <p class="text-muted mb-0">Manage your products, prices and tax rates.</p>
        </div>

        <a href="{{ route('products.create') }}" class="btn btn-primary">
            + Add Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            Please correct the validation errors.
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form method="GET" action="{{ route('products.index') }}"
                  class="row g-2 mb-4">
                <div class="col-md-8">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by product name or description"
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-auto">
                    <button class="btn btn-outline-primary">Search</button>
                    <a href="{{ route('products.index') }}"
                       class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product / Service</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Tax Rate</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $products->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $product->name }}</td>
                                <td>{{ $product->description ?: '—' }}</td>
                                <td>₹{{ number_format((float) $product->price, 2) }}</td>
                                <td>{{ number_format((float) $product->tax_rate, 2) }}%</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('products.edit', $product) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('products.destroy', $product) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No products found. Add your first product or service.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</div>
@endsection