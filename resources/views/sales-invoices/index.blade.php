@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Sales Invoices</h2>
            <p class="text-muted mb-0">Manage invoices issued to customers.</p>
        </div>
        <a href="{{ route('sales-invoices.create') }}" class="btn btn-primary">
            + Create Invoice
        </a>
    </div>

    @foreach (['success', 'info'] as $messageType)
        @if(session($messageType))
            <div class="alert alert-{{ $messageType === 'success' ? 'success' : 'info' }}">
                {{ session($messageType) }}
            </div>
        @endif
    @endforeach

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('sales-invoices.index') }}"
                  class="row g-2 mb-4">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search invoice number or customer"
                           value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-outline-primary">Search</button>
                    <a href="{{ route('sales-invoices.index') }}"
                       class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Invoice Number</th>
                            <th>Customer</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        @php
                            $displayStatus = $invoice->status;

                            if (
                                $invoice->status !== 'paid' &&
                                $invoice->due_date->lt(today())
                            ) {
                                $displayStatus = 'overdue';
                            }
                        @endphp
                        <tr>
                            <td>{{ $invoices->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                            <td>{{ $invoice->due_date->format('d M Y') }}</td>
                            <td>₹{{ number_format((float) $invoice->total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{
                                    $displayStatus === 'paid' ? 'success' :
                                    ($displayStatus === 'overdue' ? 'danger' : 'warning text-dark')
                                }}">
                                    {{ ucfirst($displayStatus) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('sales-invoices.show', $invoice) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                                <form method="POST"
                                      action="{{ route('sales-invoices.destroy', $invoice) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this invoice and its items?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No sales invoices found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection