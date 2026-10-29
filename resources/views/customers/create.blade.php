
@extends('layouts.app')

@section('title', 'Add Customer')
@section('heading', 'Add Customer')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Customer Name *</label>
                <input name="name" class="form-control"
                       value="{{ old('name') }}" required>
                @error('name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input name="company_name" class="form-control"
                       value="{{ old('company_name') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email') }}">
                @error('email')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input name="phone" class="form-control"
                       value="{{ old('phone') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control"
                          rows="3">{{ old('address') }}</textarea>
            </div>

            <button class="btn btn-primary" type="submit">Save Customer</button>
            <a href="{{ route('customers.index') }}"
               class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
