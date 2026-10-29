@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Invoice Details</h2>
            <p class="text-muted mb-0">{{ $invoice->invoice_number }}</p>
        </div>
        <a href="{{ route('sales-invoices.index') }}"
           class="btn btn-outline-secondary">Back to Invoices</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <small class="text-muted">Customer</small>
                    <div class="fw-semibold">{{ $invoice->customer->name }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Invoice Date</small>
                    <div>{{ $invoice->invoice_date->format('d M Y') }}</div>
                </div>
                <div class="col-md-4">
                    <small class="text-muted">Due Date</small>
                    <div>{{ $invoice->due_date->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product / Service</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Tax Rate</th>
                            <th>Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                <td>₹{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td>₹{{ number_format((float) $item->discount, 2) }}</td>
                                <td>{{ number_format((float) $item->tax_rate, 2) }}%</td>
                                <td>₹{{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="ms-auto" style="max-width: 320px">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>₹{{ number_format((float) $invoice->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount</span>
                    <span>₹{{ number_format((float) $invoice->discount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax</span>
                    <span>₹{{ number_format((float) $invoice->tax, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total</span>
                    <span>₹{{ number_format((float) $invoice->total, 2) }}</span>
                </div>
                <div class="mt-3">
                    Status: <strong>{{ ucfirst($invoice->status) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection