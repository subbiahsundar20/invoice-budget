<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    /**
     * Display budgets and support searching.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $budgets = Budget::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('period', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('budgets.index', compact('budgets', 'search'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        return view('budgets.create');
    }

    /**
     * Store a new budget.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'period' => ['required', 'in:monthly,yearly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        Budget::create($validated);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget created successfully.');
    }

    /**
     * Display a single budget.
     */
    public function show(Budget $budget)
    {
        return view('budgets.show', compact('budget'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Budget $budget)
    {
        return view('budgets.edit', compact('budget'));
    }

    /**
     * Update an existing budget.
     */
    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'period' => ['required', 'in:monthly,yearly'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $budget->update($validated);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget updated successfully.');
    }

    /**
     * Delete a budget.
     */
    public function destroy(Budget $budget)
    {
        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}