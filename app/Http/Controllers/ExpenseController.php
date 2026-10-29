<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Budget;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
   /**
     * Display expenses with search and pagination.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $expenses = Expense::with('budget')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhereHas('budget', function ($budgetQuery) use ($search) {
                            $budgetQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('expense_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('expenses.index', compact('expenses', 'search'));
    }

    /**
     * Show the create expense form.
     */
    public function create()
    {
        $budgets = Budget::orderBy('name')->get();

        return view('expenses.create', compact('budgets'));
    }

    /**
     * Store a new expense.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'budget_id' => ['nullable', 'exists:budgets,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'expense_date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        Expense::create($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    /**
     * Display one expense.
     */
    public function show(Expense $expense)
    {
        $expense->load('budget');

        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the edit expense form.
     */
    public function edit(Expense $expense)
    {
        $budgets = Budget::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'budgets'));
    }

    /**
     * Update an existing expense.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'budget_id' => ['nullable', 'exists:budgets,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'expense_date' => ['required', 'date'],
            'category' => ['nullable', 'string', 'max:255'],
        ]);

        $expense->update($validated);

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Delete an expense.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
