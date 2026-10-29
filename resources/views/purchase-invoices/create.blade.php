@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Create Purchase Invoice</h2>

    @include('purchase-invoices.form', [
        'action' => route('purchase-invoices.store'),
        'method' => 'POST',
        'invoice' => null,
    ])
</div>
@endsection