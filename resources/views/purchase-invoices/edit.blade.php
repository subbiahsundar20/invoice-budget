@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Edit Purchase Invoice</h2>

    @include('purchase-invoices.form', [
        'action' => route('purchase-invoices.update', $purchaseInvoice),
        'method' => 'PUT',
        'invoice' => $purchaseInvoice,
    ])
</div>
@endsection