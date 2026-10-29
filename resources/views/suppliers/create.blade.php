
@extends('layouts.app')

@section('title', 'Add Supplier')
@section('heading', 'Add Supplier')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Supplier Name *</label>
                <input name="name" class="form-control"
                       value="{{ old('name') }}" required>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
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
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
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

            <button class="btn btn-primary">Save Supplier</button>
            <a href="{{ route('suppliers.index') }}"
               class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
