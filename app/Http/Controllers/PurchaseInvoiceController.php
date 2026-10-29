<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseInvoiceController extends Controller
{
    /**
     * Display purchase invoices and allow searching.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $purchaseInvoices = PurchaseInvoice::with('supplier')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                            $supplierQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchase-invoices.index', compact(
            'purchaseInvoices',
            'search'
        ));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('purchase-invoices.create', compact(
            'suppliers',
            'products'
        ));
    }

    /**
     * Store a purchase invoice and its product items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'exists:suppliers,id',
            ],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'status' => [
                'required',
                Rule::in(['pending', 'paid', 'overdue']),
            ],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'gte:0'],
            'items.*.discount' => ['nullable', 'numeric', 'gte:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $itemRows = [];

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $gross = round($quantity * $unitPrice, 2);

                if ($discount > $gross) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => 'An item discount cannot exceed its quantity multiplied by unit price.',
                    ]);
                }

                $taxableAmount = round($gross - $discount, 2);
                $lineTax = round($taxableAmount * $taxRate / 100, 2);
                $lineTotal = round($taxableAmount + $lineTax, 2);

                $subtotal += $gross;
                $discountTotal += $discount;
                $taxTotal += $lineTax;

                $itemRows[] = [
                    'product_id' => $item['product_id'],
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

            // Generate a unique internal purchase reference.
            do {
                $invoiceNumber = 'PUR-' . now()->format('Ymd-His')
                    . '-' . strtoupper(\Illuminate\Support\Str::random(4));
            } while (
                PurchaseInvoice::where('invoice_number', $invoiceNumber)->exists()
            );

            $invoice = PurchaseInvoice::create([
                'supplier_id' => $validated['supplier_id'],
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'discount' => $discountTotal,
                'tax' => $taxTotal,
                'total' => $total,
                'status' => $validated['status'],
            ]);

            foreach ($itemRows as $itemRow) {
                $invoice->items()->create($itemRow);
            }

            return $invoice;
        });

        return redirect()
            ->route('purchase-invoices.show', $invoice)
            ->with('success', 'Purchase invoice created successfully. Reference: ' . $invoice->invoice_number);
    }

    /**
     * Display a purchase invoice and its items.
     */
    public function show(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load(['supplier', 'items.product']);

        return view('purchase-invoices.show', compact('purchaseInvoice'));
    }

    /**
     * Show the edit form.
     */
    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->load('items');

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('purchase-invoices.edit', compact(
            'purchaseInvoice',
            'suppliers',
            'products'
        ));
    }

    /**
     * Update the invoice and replace its items.
     */
    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:invoice_date'],
            'status' => [
                'required',
                Rule::in(['pending', 'paid', 'overdue']),
            ],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'distinct',
                'exists:products,id',
            ],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'gte:0'],
            'items.*.discount' => ['nullable', 'numeric', 'gte:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'between:0,100'],
        ]);

        DB::transaction(function () use ($validated, $purchaseInvoice) {
            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            $itemRows = [];

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $gross = round($quantity * $unitPrice, 2);

                if ($discount > $gross) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => 'An item discount cannot exceed its quantity multiplied by unit price.',
                    ]);
                }

                $taxableAmount = round($gross - $discount, 2);
                $lineTax = round($taxableAmount * $taxRate / 100, 2);
                $lineTotal = round($taxableAmount + $lineTax, 2);

                $subtotal += $gross;
                $discountTotal += $discount;
                $taxTotal += $lineTax;

                $itemRows[] = [
                    'product_id' => $item['product_id'],
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

            $purchaseInvoice->update([
                'supplier_id' => $validated['supplier_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'discount' => $discountTotal,
                'tax' => $taxTotal,
                'total' => $total,
                'status' => $validated['status'],
            ]);

            // Remove old rows, then save the updated rows.
            $purchaseInvoice->items()->delete();

            foreach ($itemRows as $itemRow) {
                $purchaseInvoice->items()->create($itemRow);
            }
        });

        return redirect()
            ->route('purchase-invoices.show', $purchaseInvoice)
            ->with('success', 'Purchase invoice updated successfully.');
    }

    /**
     * Delete a purchase invoice.
     */
    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        $purchaseInvoice->delete();

        return redirect()
            ->route('purchase-invoices.index')
            ->with('success', 'Purchase invoice deleted successfully.');
    }
}