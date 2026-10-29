<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Carbon\Carbon;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
     public function index()
    {
        $today = Carbon::today();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $startOfYear = $today->copy()->startOfYear();
        $endOfYear = $today->copy()->endOfYear();

        // Overall totals
        $totalSales = SalesInvoice::sum('total');
        $totalPurchases = PurchaseInvoice::sum('total');
        $totalExpenses = Expense::sum('amount');
        $totalBudget = Budget::sum('amount');

        // Invoice counts
        $salesInvoiceCount = SalesInvoice::count();
        $purchaseInvoiceCount = PurchaseInvoice::count();

        // Unpaid invoice totals
        $outstandingSales = SalesInvoice::whereIn('status', ['pending', 'overdue'])
            ->sum('total');

        $outstandingPurchases = PurchaseInvoice::whereIn('status', ['pending', 'overdue'])
            ->sum('total');

        // Current month totals
        $monthlySales = SalesInvoice::whereBetween('invoice_date', [
            $startOfMonth,
            $endOfMonth,
        ])->sum('total');

        $monthlyPurchases = PurchaseInvoice::whereBetween('invoice_date', [
            $startOfMonth,
            $endOfMonth,
        ])->sum('total');

        $monthlyExpenses = Expense::whereBetween('expense_date', [
            $startOfMonth,
            $endOfMonth,
        ])->sum('amount');

        // Budget-linked expenses in the current month
        $monthlyBudgetExpenses = Expense::whereNotNull('budget_id')
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Monthly sales and purchases for the current year
        $yearSalesInvoices = SalesInvoice::whereBetween('invoice_date', [
            $startOfYear,
            $endOfYear,
        ])->get(['invoice_date', 'total']);

        $yearPurchaseInvoices = PurchaseInvoice::whereBetween('invoice_date', [
            $startOfYear,
            $endOfYear,
        ])->get(['invoice_date', 'total']);

        $monthlyChart = collect(range(1, 12))->map(function ($month) use (
            $yearSalesInvoices,
            $yearPurchaseInvoices
        ) {
            return [
                'month' => Carbon::create(2000, $month, 1)->format('M'),
                'sales' => (float) $yearSalesInvoices
                    ->filter(fn ($invoice) => $invoice->invoice_date->month === $month)
                    ->sum('total'),
                'purchases' => (float) $yearPurchaseInvoices
                    ->filter(fn ($invoice) => $invoice->invoice_date->month === $month)
                    ->sum('total'),
            ];
        });

        // Recent invoices
        $recentSales = SalesInvoice::latest('created_at')
            ->take(5)
            ->get();

        $recentPurchases = PurchaseInvoice::latest('created_at')
            ->take(5)
            ->get();

        
        // Fetch currency exchange rates and cache them for 6 hours.
        $currencyData = Cache::remember(
            'dashboard_currency_rates',
            now()->addHours(6),
            function () {
     try {
    $response = Http::timeout(15)->get(
        'https://api.frankfurter.dev/v2/rates',
        [
            'base' => 'USD',
            'quotes' => 'INR,EUR,GBP',
        ]
    );

    \Illuminate\Support\Facades\Log::info(
        'Currency API response',
        [
            'status' => $response->status(),
            'body' => $response->body(),
        ]
    );

    if (! $response->successful()) {
        return ['rates' => [], 'date' => null];
    }

    $rows = collect($response->json());

    return [
        'rates' => $rows->mapWithKeys(function ($row) {
            return [$row['quote'] => (float) $row['rate']];
        })->all(),
        'date' => $rows->first()['date'] ?? null,
    ];
} catch (\Throwable $e) {
    \Illuminate\Support\Facades\Log::error(
        'Currency API request failed',
        [
            'message' => $e->getMessage(),
            'exception' => get_class($e),
        ]
    );

    return ['rates' => [], 'date' => null];
}
            }
        );

        $currencyRates = $currencyData['rates'];
        $currencyRateDate = $currencyData['date'];


        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'totalBudget',
            'salesInvoiceCount',
            'purchaseInvoiceCount',
            'outstandingSales',
            'outstandingPurchases',
            'monthlySales',
            'monthlyPurchases',
            'monthlyExpenses',
            'monthlyBudgetExpenses',
            'monthlyChart',
            'recentSales',
            'recentPurchases',
             'recentSales',
            'recentPurchases',
            'currencyRates',
            'currencyRateDate'
        ));
    }
}
