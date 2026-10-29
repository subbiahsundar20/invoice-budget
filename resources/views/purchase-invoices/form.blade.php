<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Invoice Details</div>
        <div class="card-body">
            @if($invoice)
                <div class="mb-3">
                    <label class="form-label">Internal Reference</label>
                    <input type="text" class="form-control"
                           value="{{ $invoice->invoice_number }}" disabled>
                    <small class="text-muted">The reference cannot be changed.</small>
                </div>
            @else
                <div class="alert alert-info">
                    An internal purchase reference will be generated automatically when you save.
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="supplier_id" class="form-label">Supplier *</label>
                    <select name="supplier_id" id="supplier_id" class="form-select" required>
                        <option value="">Select supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                @selected(old('supplier_id', $invoice?->supplier_id) == $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="invoice_date" class="form-label">Invoice Date *</label>
                    <input type="date" name="invoice_date" id="invoice_date"
                           class="form-control" required
                           value="{{ old('invoice_date', $invoice?->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="due_date" class="form-label">Due Date *</label>
                    <input type="date" name="due_date" id="due_date"
                           class="form-control" required
                           value="{{ old('due_date', $invoice?->due_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Payment Status *</label>
                    <select name="status" id="status" class="form-select" required>
                        @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'overdue' => 'Overdue'] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('status', $invoice?->status ?? 'pending') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Products / Items</span>
            <button type="button" id="add-item" class="btn btn-sm btn-primary">
                + Add Product
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="items-table">
                    <thead>
                        <tr>
                            <th style="min-width: 180px;">Product</th>
                            <th style="min-width: 100px;">Quantity</th>
                            <th style="min-width: 130px;">Unit Price (₹)</th>
                            <th style="min-width: 120px;">Discount (₹)</th>
                            <th style="min-width: 110px;">Tax (%)</th>
                            <th style="min-width: 130px;">Line Total (₹)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        @php
                            $formItems = old('items');

                            if ($formItems === null) {
                                $formItems = $invoice
                                    ? $invoice->items->map(fn ($item) => [
                                        'product_id' => $item->product_id,
                                        'quantity' => $item->quantity,
                                        'unit_price' => $item->unit_price,
                                        'discount' => $item->discount,
                                        'tax_rate' => $item->tax_rate,
                                    ])->toArray()
                                    : [[
                                        'product_id' => '',
                                        'quantity' => 1,
                                        'unit_price' => '',
                                        'discount' => 0,
                                        'tax_rate' => 0,
                                    ]];
                            }
                        @endphp

                        @foreach($formItems as $i => $item)
                            <tr class="item-row">
                                <td>
                                    <select name="items[{{ $i }}][product_id]"
                                            class="form-select product-select" required>
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-price="{{ $product->price }}"
                                                data-tax="{{ $product->tax_rate }}"
                                                @selected(($item['product_id'] ?? '') == $product->id)>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][quantity]"
                                           class="form-control quantity" min="0.01"
                                           step="0.01" required
                                           value="{{ $item['quantity'] ?? 1 }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][unit_price]"
                                           class="form-control unit-price" min="0"
                                           step="0.01" required
                                           value="{{ $item['unit_price'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][discount]"
                                           class="form-control discount" min="0"
                                           step="0.01"
                                           value="{{ $item['discount'] ?? 0 }}">
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][tax_rate]"
                                           class="form-control tax-rate" min="0"
                                           max="100" step="0.01"
                                           value="{{ $item['tax_rate'] ?? 0 }}">
                                </td>
                                <td>
                                    <span class="line-total">₹0.00</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <template id="item-template">
                <tr class="item-row">
                    <td>
                        <select class="form-select product-select" data-field="product_id" required>
                            <option value="">Select product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-price="{{ $product->price }}"
                                        data-tax="{{ $product->tax_rate }}">
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control quantity" data-field="quantity" min="0.01" step="0.01" value="1" required></td>
                    <td><input type="number" class="form-control unit-price" data-field="unit_price" min="0" step="0.01" required></td>
                    <td><input type="number" class="form-control discount" data-field="discount" min="0" step="0.01" value="0"></td>
                    <td><input type="number" class="form-control tax-rate" data-field="tax_rate" min="0" max="100" step="0.01" value="0"></td>
                    <td><span class="line-total">₹0.00</span></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-item">Remove</button></td>
                </tr>
            </template>

            <div class="row justify-content-end mt-3">
                <div class="col-md-4">
                    <table class="table">
                        <tr>
                            <th>Subtotal</th>
                            <td class="text-end" id="subtotal-display">₹0.00</td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td class="text-end" id="discount-display">₹0.00</td>
                        </tr>
                        <tr>
                            <th>Tax</th>
                            <td class="text-end" id="tax-display">₹0.00</td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <th class="text-end" id="total-display">₹0.00</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success">
        {{ $invoice ? 'Update Purchase Invoice' : 'Save Purchase Invoice' }}
    </button>
    <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary">
        Cancel
    </a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.getElementById('items-body');
    const template = document.getElementById('item-template');
    const addButton = document.getElementById('add-item');

    function renumberRows() {
        body.querySelectorAll('.item-row').forEach((row, index) => {
            row.querySelectorAll('[name], [data-field]').forEach((field) => {
                const fieldName = field.dataset.field
                    || field.name.match(/\[([^\]]+)\]$/)?.[1];

                if (fieldName) {
                    field.name = `items[${index}][${fieldName}]`;
                }
            });
        });
    }

    function calculateTotals() {
        let subtotal = 0;
        let discounts = 0;
        let taxes = 0;

        body.querySelectorAll('.item-row').forEach((row) => {
            const quantity = Math.max(0, parseFloat(row.querySelector('.quantity').value) || 0);
            const price = Math.max(0, parseFloat(row.querySelector('.unit-price').value) || 0);
            const discount = Math.max(0, parseFloat(row.querySelector('.discount').value) || 0);
            const taxRate = Math.min(100, Math.max(0, parseFloat(row.querySelector('.tax-rate').value) || 0));

            const gross = quantity * price;
            const appliedDiscount = Math.min(discount, gross);
            const tax = (gross - appliedDiscount) * taxRate / 100;
            const lineTotal = gross - appliedDiscount + tax;

            subtotal += gross;
            discounts += appliedDiscount;
            taxes += tax;

            row.querySelector('.line-total').textContent =
                '₹' + lineTotal.toFixed(2);
        });

        document.getElementById('subtotal-display').textContent = '₹' + subtotal.toFixed(2);
        document.getElementById('discount-display').textContent = '₹' + discounts.toFixed(2);
        document.getElementById('tax-display').textContent = '₹' + taxes.toFixed(2);
        document.getElementById('total-display').textContent =
            '₹' + (subtotal - discounts + taxes).toFixed(2);
    }

    addButton.addEventListener('click', function () {
        const fragment = template.content.cloneNode(true);
        body.appendChild(fragment);
        renumberRows();
        calculateTotals();
    });

    body.addEventListener('change', function (event) {
        const row = event.target.closest('.item-row');

        if (row && event.target.classList.contains('product-select')) {
            const option = event.target.selectedOptions[0];

            if (option && option.value) {
                row.querySelector('.unit-price').value = option.dataset.price || 0;
                row.querySelector('.tax-rate').value = option.dataset.tax || 0;
            }
        }

        calculateTotals();
    });

    body.addEventListener('input', calculateTotals);

    body.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item')) {
            const rows = body.querySelectorAll('.item-row');

            if (rows.length === 1) {
                alert('At least one product is required.');
                return;
            }

            event.target.closest('.item-row').remove();
            renumberRows();
            calculateTotals();
        }
    });

    renumberRows();
    calculateTotals();
});
</script>