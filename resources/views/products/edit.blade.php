@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h2 class="fw-bold">Edit Product</h2>
        <p class="text-muted">Update the product or service details.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('products.update', $product) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Product / Service Name *</label>
                    <input type="text" name="name"
                           class="form-control"
                           value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"
                              rows="3">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Price (₹) *</label>
                        <input type="number" name="price"
                               class="form-control"
                               step="0.01" min="0"
                               value="{{ old('price', $product->price) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tax Rate (%) *</label>
                        <input type="number" name="tax_rate"
                               class="form-control"
                               step="0.01" min="0" max="100"
                               value="{{ old('tax_rate', $product->tax_rate) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Status *</label>
                    <select name="is_active" class="form-select" required>
                        <option value="1"
                            {{ (string) old('is_active', (int) $product->is_active) === '1' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0"
                            {{ (string) old('is_active', (int) $product->is_active) === '0' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('products.index') }}"
                   class="btn btn-outline-secondary">Cancel</a>
            </form>

        </div>
    </div>
</div>
@endsection