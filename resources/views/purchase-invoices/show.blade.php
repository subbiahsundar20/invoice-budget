@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Purchase Invoice Details</h2>
            <p class="text-muted mb-0">{{ $purchaseInvoice->invoice_number }}</p>
        </div>
        <div>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">
                Back
            </a>
            <a href="{{ route('purchase-invoices.edit', $purchaseInvoice) }}"
               class="btn btn-warning">Edit</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Invoice Information</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Internal Reference:</strong>
                    {{ $purchaseInvoice->invoice_number }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Supplier:</strong>
                    {{ $purchaseInvoice->supplier->name }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Invoice Date:</strong>
                    {{ $purchaseInvoice->invoice_date->format('d M Y') }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Due Date:</strong>
                    {{ $purchaseInvoice->due_date->format('d M Y') }}
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Status:</strong>
                    {{ ucfirst($purchaseInvoice->status) }}
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Purchased Products</div>
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>Tax Rate</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseInvoice->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format((float) $item->quantity, 2) }}</td>
                            <td>₹{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td>₹{{ number_format((float) $item->discount, 2) }}</td>
                            <td>{{ number_format((float) $item->tax_rate, 2) }}%</td>
                            <td>₹{{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-4">
            <table class="table table-bordered">
                <tr>
                    <th>Subtotal</th>
                    <td class="text-end">
                        ₹{{ number_format((float) $purchaseInvoice->subtotal, 2) }}
                    </td>
                </tr>
                <tr>
                    <th>Discount</th>
                    <td class="text-end">
                        ₹{{ number_format((float) $purchaseInvoice->discount, 2) }}
                    </td>
                </tr>
                <tr>
                    <th>Tax</th>
                    <td class="text-end">
                        ₹{{ number_format((float) $purchaseInvoice->tax, 2) }}
                    </td>
                </tr>
                <tr>
                    <th>Total</th>
                    <th class="text-end">
                        ₹{{ number_format((float) $purchaseInvoice->total, 2) }}
                    </th>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection