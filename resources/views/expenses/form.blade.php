
<div class="mb-3">
    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
    <input
        type="text"
        name="description"
        id="description"
        class="form-control @error('description') is-invalid @enderror"
        value="{{ old('description', $expense->description ?? '') }}"
        required
        maxlength="255"
    >
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="amount" class="form-label">Amount (₹) <span class="text-danger">*</span></label>
        <input
            type="number"
            name="amount"
            id="amount"
            class="form-control @error('amount') is-invalid @enderror"
            value="{{ old('amount', $expense->amount ?? '') }}"
            min="0.01"
            step="0.01"
            required
        >
        @error('amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
        <input
            type="date"
            name="expense_date"
            id="expense_date"
            class="form-control @error('expense_date') is-invalid @enderror"
            value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
            required
        >
        @error('expense_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="category" class="form-label">Category</label>
    <input
        type="text"
        name="category"
        id="category"
        class="form-control @error('category') is-invalid @enderror"
        value="{{ old('category', $expense->category ?? '') }}"
        maxlength="255"
        placeholder="e.g. Travel, Office Supplies, Utilities"
    >
    @error('category')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="budget_id" class="form-label">Link to Budget (optional)</label>
    <select
        name="budget_id"
        id="budget_id"
        class="form-select @error('budget_id') is-invalid @enderror"
    >
        <option value="">No budget linked</option>
        @foreach ($budgets as $budget)
            <option
                value="{{ $budget->id }}"
                @selected((string) old('budget_id', $expense->budget_id ?? '') === (string) $budget->id)
            >
                {{ $budget->name }} — ₹{{ number_format((float) $budget->amount, 2) }}
            </option>
        @endforeach
    </select>
    @error('budget_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
</div>
