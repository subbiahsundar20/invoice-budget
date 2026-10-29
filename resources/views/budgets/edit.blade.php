@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Edit Budget</h2>

    @include('budgets.form', [
        'action' => route('budgets.update', $budget),
        'method' => 'PUT',
        'budget' => $budget,
    ])
</div>
@endsection