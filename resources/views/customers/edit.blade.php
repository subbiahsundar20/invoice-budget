
@extends('layouts.app')

@section('title', 'Edit Customer')
@section('heading', 'Edit Customer')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST"
              action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Customer Name *</label>
                <input type="text" name="name" class="form-control"
                       value="{{ old('name', $customer->name) }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control"
                       value="{{ old('company_name', $customer->company_name) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $customer->email) }}">
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control"
                       value="{{ old('phone', $customer->phone) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"
                          rows="4">{{ old('address', $customer->address) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                Update Customer
            </button>

            <a href="{{ route('customers.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>
        </form>
    </div>
</div>
@endsection
