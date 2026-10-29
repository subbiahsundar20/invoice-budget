
@extends('layouts.app')

@section('title', 'Edit Supplier')
@section('heading', 'Edit Supplier')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Supplier Name *</label>
                <input name="name" class="form-control"
                       value="{{ old('name', $supplier->name) }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input name="company_name" class="form-control"
                       value="{{ old('company_name', $supplier->company_name) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $supplier->email) }}">
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control"
                       value="{{ old('phone', $supplier->phone) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"
                          rows="3">{{ old('address', $supplier->address) }}</textarea>
            </div>

            <button class="btn btn-primary">Update Supplier</button>
            <a href="{{ route('suppliers.index') }}"
               class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
