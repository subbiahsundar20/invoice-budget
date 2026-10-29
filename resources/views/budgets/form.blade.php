<form action="{{ $action }}" method="POST">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct these errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            Budget Information
        </div>

        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Budget Name *</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control"
                    value="{{ old('name', $budget?->name) }}"
                    placeholder="e.g. Marketing Budget"
                    maxlength="255"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input
                    type="text"
                    name="category"
                    id="category"
                    class="form-control"
                    value="{{ old('category', $budget?->category) }}"
                    placeholder="e.g. Marketing, Office, Travel"
                    maxlength="255"
                >
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Budget Amount (₹) *</label>
                <input
                    type="number"
                    name="amount"
                    id="amount"
                    class="form-control"
                    min="0.01"
                    step="0.01"
                    value="{{ old('amount', $budget?->amount) }}"
                    placeholder="Enter budget amount"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="period" class="form-label">Budget Period *</label>
                <select name="period" id="period" class="form-select" required>
                    <option value="">Select period</option>

                    <option value="monthly"
                        @selected(old('period', $budget?->period) === 'monthly')>
                        Monthly
                    </option>

                    <option value="yearly"
                        @selected(old('period', $budget?->period) === 'yearly')>
                        Yearly
                    </option>
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Start Date *</label>
                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        class="form-control"
                        value="{{ old('start_date', $budget?->start_date?->format('Y-m-d')) }}"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">End Date *</label>
                    <input
                        type="date"
                        name="end_date"
                        id="end_date"
                        class="form-control"
                        value="{{ old('end_date', $budget?->end_date?->format('Y-m-d')) }}"
                        required
                    >
                </div>
            </div>

            <div class="alert alert-info">
                Choose dates covering the period you want this budget to apply to.
                The application will validate that the end date is not before the start date.
            </div>

            <button type="submit" class="btn btn-success">
                {{ $budget ? 'Update Budget' : 'Save Budget' }}
            </button>

            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </div>
</form>