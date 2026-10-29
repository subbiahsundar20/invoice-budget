<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = SalesInvoice::with('customer')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales-invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('sales-invoices.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:99999999',
            ],
            'items.*.discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $calculatedItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::where('is_active', true)
                    ->find($item['product_id']);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => 'One of the selected products is inactive or unavailable.',
                    ]);
                }

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $product->price;
                $taxRate = (float) $product->tax_rate;
                $discount = (float) ($item['discount'] ?? 0);

                $gross = round($quantity * $unitPrice, 2);

                if ($discount > $gross) {
                    throw ValidationException::withMessages([
                        'items' => "The discount for {$product->name} cannot exceed its line amount.",
                    ]);
                }

                $taxableAmount = round($gross - $discount, 2);
                $lineTax = round($taxableAmount * $taxRate / 100, 2);
                $lineTotal = round($taxableAmount + $lineTax, 2);

                $subtotal += $gross;
                $discountTotal += $discount;
                $taxTotal += $lineTax;

                $calculatedItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax_rate' => $taxRate,
                    'line_total' => $lineTotal,
                ];
            }

            $subtotal = round($subtotal, 2);
            $discountTotal = round($discountTotal, 2);
            $taxTotal = round($taxTotal, 2);
            $total = round($subtotal - $discountTotal + $taxTotal, 2);

            $invoice = SalesInvoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => 'INV-' . now()->format('YmdHis')
                    . '-' . strtoupper(bin2hex(random_bytes(3))),
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'discount' => $discountTotal,
                'tax' => $taxTotal,
                'total' => $total,
                'status' => 'pending',
            ]);

            $invoice->items()->createMany($calculatedItems);
        });

        return redirect()
            ->route('sales-invoices.index')
            ->with('success', 'Sales invoice created successfully.');
    }

    public function show(SalesInvoice $sales_invoice)
    {
        $sales_invoice->load(['customer', 'items.product']);

        return view('sales-invoices.show', [
            'invoice' => $sales_invoice,
        ]);
    }

    public function edit(SalesInvoice $sales_invoice)
    {
        return redirect()
            ->route('sales-invoices.show', $sales_invoice)
            ->with('info', 'Invoice editing will be added in the next step.');
    }

    public function update(Request $request, SalesInvoice $sales_invoice)
    {
        return redirect()
            ->route('sales-invoices.show', $sales_invoice)
            ->with('info', 'Invoice editing will be added in the next step.');
    }

    public function destroy(SalesInvoice $sales_invoice)
    {
        $sales_invoice->delete();

        return redirect()
            ->route('sales-invoices.index')
            ->with('success', 'Sales invoice deleted successfully.');
    }
}