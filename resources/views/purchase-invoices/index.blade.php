@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Purchase Invoices</h2>
            <p class="text-muted mb-0">Manage supplier purchases and payment status.</p>
        </div>
        <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary">
            + Create Purchase Invoice
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('purchase-invoices.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control"
                   placeholder="Search reference, supplier, or status..."
                   value="{{ $search }}">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary">
                Reset
            </a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Supplier</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="min-width: 190px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseInvoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->supplier->name }}</td>
                            <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                            <td>{{ $invoice->due_date->format('d M Y') }}</td>
                            <td>₹{{ number_format((float) $invoice->total, 2) }}</td>
                            <td>
                                <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('purchase-invoices.show', $invoice) }}"
                                   class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('purchase-invoices.edit', $invoice) }}"
                                   class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('purchase-invoices.destroy', $invoice) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this purchase invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No purchase invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $purchaseInvoices->links() }}
    </div>
</div>
@endsection