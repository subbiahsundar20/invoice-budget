
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <h1 class="h3 mb-1">Financial Dashboard</h1>
        <p class="text-muted mb-0">
            Overview of your invoices, budgets and expenses.
        </p>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Total Sales</div>
                    <div class="h4 text-success mt-2">
                        ₹{{ number_format((float) $totalSales, 2) }}
                    </div>
                    <div class="small text-muted">
                        {{ $salesInvoiceCount }} sales invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Total Purchases</div>
                    <div class="h4 text-primary mt-2">
                        ₹{{ number_format((float) $totalPurchases, 2) }}
                    </div>
                    <div class="small text-muted">
                        {{ $purchaseInvoiceCount }} purchase invoices
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Total Expenses</div>
                    <div class="h4 text-danger mt-2">
                        ₹{{ number_format((float) $totalExpenses, 2) }}
                    </div>
                    <div class="small text-muted">All recorded expenses</div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body">
                    <div class="text-muted small">Total Budget Allocation</div>
                    <div class="h4 mt-2">
                        ₹{{ number_format((float) $totalBudget, 2) }}
                    </div>
                    <div class="small text-muted">All configured budgets</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Outstanding and current month --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Outstanding Sales</div>
                    <div class="h5 text-warning mt-2">
                        ₹{{ number_format((float) $outstandingSales, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Outstanding Purchases</div>
                    <div class="h5 text-warning mt-2">
                        ₹{{ number_format((float) $outstandingPurchases, 2) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Sales This Month</div>
                    <div class="h5 text-success mt-2">
                        ₹{{ number_format((float) $monthlySales, 2) }}
                    </div>
                    <div class="small text-muted">{{ now()->format('F Y') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Expenses This Month</div>
                    <div class="h5 text-danger mt-2">
                        ₹{{ number_format((float) $monthlyExpenses, 2) }}
                    </div>
                    <div class="small text-muted">{{ now()->format('F Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly comparison --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h5 mb-0">Monthly Sales vs Purchases — {{ now()->year }}</h2>
        </div>

        <div class="card-body">
            @php
                $maxMonthlyValue = max(
                    1,
                    (float) $monthlyChart->max(fn ($row) => max($row['sales'], $row['purchases']))
                );
            @endphp

            <div class="d-flex flex-wrap gap-3 mb-3 small">
                <span><span class="badge bg-success">&nbsp;</span> Sales</span>
                <span><span class="badge bg-primary">&nbsp;</span> Purchases</span>
            </div>

            @foreach ($monthlyChart as $row)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>{{ $row['month'] }}</span>
                        <span>
                            Sales: ₹{{ number_format($row['sales'], 2) }}
                            &nbsp;|&nbsp;
                            Purchases: ₹{{ number_format($row['purchases'], 2) }}
                        </span>
                    </div>

                    <div class="progress mb-1" style="height: 10px;">
                        <div class="progress-bar bg-success"
                             role="progressbar"
                             style="width: {{ min(100, ($row['sales'] / $maxMonthlyValue) * 100) }}%"
                             aria-label="Sales">
                        </div>
                    </div>

                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary"
                             role="progressbar"
                             style="width: {{ min(100, ($row['purchases'] / $maxMonthlyValue) * 100) }}%"
                             aria-label="Purchases">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent invoices --}}
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Recent Sales Invoices</h2>
                        <a href="{{ route('sales-invoices.index') }}"
                           class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSales as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        ₹{{ number_format((float) $invoice->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No sales invoices yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Recent Purchase Invoices</h2>
                        <a href="{{ route('purchase-invoices.index') }}"
                           class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPurchases as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        ₹{{ number_format((float) $invoice->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        No purchase invoices yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Public Currency Exchange API --}}
<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-white py-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h2 class="h5 mb-1">Currency Exchange Rates</h2>
                <p class="text-muted small mb-0">
                    Reference rates against 1 US Dollar (USD)
                </p>
            </div>

            <span class="badge bg-info text-dark">Public API</span>
        </div>
    </div>

    <div class="card-body">
        @if (!empty($currencyRates))
            <div class="row g-3">
                @foreach ([
                    'INR' => 'Indian Rupee',
                    'EUR' => 'Euro',
                    'GBP' => 'British Pound',
                ] as $code => $name)
                    <div class="col-sm-6 col-lg-4">
                        <div class="border rounded p-3 h-100">
                            <div class="text-muted small">{{ $name }}</div>
                            <div class="h4 mt-2 mb-1">
                                {{ $code }}
                                {{ isset($currencyRates[$code])
                                    ? number_format($currencyRates[$code], 4)
                                    : 'Not available' }}
                            </div>
                            <div class="small text-muted">
                                1 USD = {{ $code }}
                                {{ isset($currencyRates[$code])
                                    ? number_format($currencyRates[$code], 4)
                                    : '—' }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="small text-muted mt-3 mb-0">
                Rate date:
                {{ $currencyRateDate
                    ? \Carbon\Carbon::parse($currencyRateDate)->format('d M Y')
                    : 'Not provided' }}
                · Source:
                <a href="https://frankfurter.dev/" target="_blank" rel="noopener noreferrer">
                    Frankfurter
                </a>
            </p>
        @else
            <div class="alert alert-warning mb-0">
                Currency exchange rates are temporarily unavailable.
                Please try again later.
            </div>
        @endif
    </div>
</div>

@endsection
