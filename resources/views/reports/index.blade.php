
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Financial Reports</h1>
            <p class="text-muted mb-0">Review financial activity and budget utilization.</p>
        </div>

        <a href="{{ route('reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
           class="btn btn-success">
            Download CSV
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Date filters --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date"
                           class="form-control" value="{{ $startDate }}" required>
                </div>

                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date"
                           class="form-control" value="{{ $endDate }}" required>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-light border mb-4">
        Report period:
        <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
        to
        <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>
    </div>

    {{-- Financial summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Sales</div>
                    <div class="h4 text-success mt-2">
                        ₹{{ number_format((float) $totalSales, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Purchases</div>
                    <div class="h4 text-primary mt-2">
                        ₹{{ number_format((float) $totalPurchases, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Expenses</div>
                    <div class="h4 text-danger mt-2">
                        ₹{{ number_format((float) $totalExpenses, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Net Balance</div>
                    <div class="h4 mt-2 {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                        ₹{{ number_format((float) $netBalance, 2) }}
                    </div>
                    <div class="small text-muted">Sales − Purchases − Expenses</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding invoices --}}
    <h2 class="h5 mb-3">Outstanding Invoices</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small">Outstanding Sales</div>
                    <div class="h4 text-warning mt-2">
                        ₹{{ number_format((float) $outstandingSales, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted small">Outstanding Purchases</div>
                    <div class="h4 text-warning mt-2">
                        ₹{{ number_format((float) $outstandingPurchases, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Budget summary --}}
    <h2 class="h5 mb-3">Budget Utilization</h2>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">Allocated Budget</div>
                    <div class="h5 mt-2">
                        ₹{{ number_format((float) $totalBudget, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">Linked Budget Expenses</div>
                    <div class="h5 text-danger mt-2">
                        ₹{{ number_format((float) $totalBudgetExpenses, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="text-muted small">Budget Remaining</div>
                    <div class="h5 {{ $budgetRemaining >= 0 ? 'text-success' : 'text-danger' }} mt-2">
                        ₹{{ number_format((float) $budgetRemaining, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Budget details --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h2 class="h5 mb-0">Budget Details</h2>
            <div class="small text-muted mt-1">
                Expenses are counted by expense date within the selected period.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Budget</th>
                        <th>Period</th>
                        <th class="text-end">Allocated</th>
                        <th class="text-end">Expenses</th>
                        <th class="text-end">Remaining</th>
                        <th style="min-width: 170px;">Utilization</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($budgetRows as $row)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $row['budget']->name }}</div>
                                <div class="small text-muted">
                                    {{ $row['budget']->category ?: 'Uncategorized' }}
                                </div>
                            </td>

                            <td>{{ ucfirst($row['budget']->period) }}</td>

                            <td class="text-end text-nowrap">
                                ₹{{ number_format($row['allocated'], 2) }}
                            </td>

                            <td class="text-end text-nowrap">
                                ₹{{ number_format($row['spent'], 2) }}
                            </td>

                            <td class="text-end text-nowrap {{ $row['remaining'] < 0 ? 'text-danger' : '' }}">
                                ₹{{ number_format($row['remaining'], 2) }}
                            </td>

                            <td>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ number_format($row['percentage'], 2) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $row['percentage'] > 100 ? 'bg-danger' : 'bg-success' }}"
                                         role="progressbar"
                                         style="width: {{ min(100, $row['percentage']) }}%">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No budgets overlap the selected report period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
