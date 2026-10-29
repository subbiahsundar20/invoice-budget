@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Create Budget</h2>

    @include('budgets.form', [
        'action' => route('budgets.store'),
        'method' => 'POST',
        'budget' => null,
    ])
</div>
@endsection