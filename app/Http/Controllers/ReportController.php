<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
   public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Default to the current calendar month.
        $startDate = $validated['start_date']
            ?? now()->startOfMonth()->toDateString();

        $endDate = $validated['end_date']
            ?? now()->endOfMonth()->toDateString();

        // Financial totals for the selected period.
        $totalSales = SalesInvoice::whereBetween('invoice_date', [
            $startDate, $endDate,
        ])->sum('total');

        $totalPurchases = PurchaseInvoice::whereBetween('invoice_date', [
            $startDate, $endDate,
        ])->sum('total');

        $totalExpenses = Expense::whereBetween('expense_date', [
            $startDate, $endDate,
        ])->sum('amount');

        // This is a simple cash-flow-style comparison, not an accounting profit.
        $netBalance = $totalSales - $totalPurchases - $totalExpenses;

        $outstandingSales = SalesInvoice::whereIn('status', ['pending', 'overdue'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->sum('total');

        $outstandingPurchases = PurchaseInvoice::whereIn('status', ['pending', 'overdue'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->sum('total');

        // Include budgets whose date ranges overlap the report period.
        $budgets = Budget::whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->orderBy('name')
            ->get();

        // Sum expenses within the selected period for each budget.
        $budgetExpenses = Expense::selectRaw('budget_id, SUM(amount) as total_expenses')
            ->whereNotNull('budget_id')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('budget_id')
            ->pluck('total_expenses', 'budget_id');

        $budgetRows = $budgets->map(function ($budget) use ($budgetExpenses) {
            $spent = (float) ($budgetExpenses[$budget->id] ?? 0);
            $allocated = (float) $budget->amount;

            return [
                'budget' => $budget,
                'allocated' => $allocated,
                'spent' => $spent,
                'remaining' => $allocated - $spent,
                'percentage' => $allocated > 0
                    ? round(($spent / $allocated) * 100, 2)
                    : 0,
            ];
        });

        $totalBudget = $budgetRows->sum('allocated');
        $totalBudgetExpenses = $budgetRows->sum('spent');
        $budgetRemaining = $totalBudget - $totalBudgetExpenses;

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'netBalance',
            'outstandingSales',
            'outstandingPurchases',
            'budgetRows',
            'totalBudget',
            'totalBudgetExpenses',
            'budgetRemaining'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date']
            ?? now()->startOfMonth()->toDateString();

        $endDate = $validated['end_date']
            ?? now()->endOfMonth()->toDateString();

        $sales = SalesInvoice::whereBetween('invoice_date', [
            $startDate, $endDate,
        ])->sum('total');

        $purchases = PurchaseInvoice::whereBetween('invoice_date', [
            $startDate, $endDate,
        ])->sum('total');

        $expenses = Expense::whereBetween('expense_date', [
            $startDate, $endDate,
        ])->sum('amount');

        $budgets = Budget::whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->orderBy('name')
            ->get();

        $budgetExpenses = Expense::selectRaw('budget_id, SUM(amount) as total_expenses')
            ->whereNotNull('budget_id')
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->groupBy('budget_id')
            ->pluck('total_expenses', 'budget_id');

        $filename = "financial-report-{$startDate}-to-{$endDate}.csv";

        return response()->streamDownload(function () use (
            $startDate,
            $endDate,
            $sales,
            $purchases,
            $expenses,
            $budgets,
            $budgetExpenses
        ) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM helps Excel recognize UTF-8 CSV content.
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['Financial Report']);
            fputcsv($file, ['Start Date', $startDate]);
            fputcsv($file, ['End Date', $endDate]);
            fputcsv($file, []);
            fputcsv($file, ['Financial Summary', 'Amount (INR)']);
            fputcsv($file, ['Sales', number_format((float) $sales, 2, '.', '')]);
            fputcsv($file, ['Purchases', number_format((float) $purchases, 2, '.', '')]);
            fputcsv($file, ['Expenses', number_format((float) $expenses, 2, '.', '')]);
            fputcsv($file, [
                'Net Balance',
                number_format((float) ($sales - $purchases - $expenses), 2, '.', ''),
            ]);

            fputcsv($file, []);
            fputcsv($file, [
                'Budget',
                'Allocated (INR)',
                'Expenses in Period (INR)',
                'Remaining (INR)',
                'Utilization (%)',
            ]);

            foreach ($budgets as $budget) {
                $allocated = (float) $budget->amount;
                $spent = (float) ($budgetExpenses[$budget->id] ?? 0);

                fputcsv($file, [
                    $budget->name,
                    number_format($allocated, 2, '.', ''),
                    number_format($spent, 2, '.', ''),
                    number_format($allocated - $spent, 2, '.', ''),
                    $allocated > 0
                        ? number_format(($spent / $allocated) * 100, 2, '.', '')
                        : '0.00',
                ]);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}