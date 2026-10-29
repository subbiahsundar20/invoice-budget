<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('invoice_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('sales_invoice_id')->nullable()
            ->constrained('sales_invoices')->cascadeOnDelete();

        $table->foreignId('purchase_invoice_id')->nullable()
            ->constrained('purchase_invoices')->cascadeOnDelete();

        $table->foreignId('product_id')
            ->constrained('products')->restrictOnDelete();

        $table->decimal('quantity', 12, 2);
        $table->decimal('unit_price', 12, 2);
        $table->decimal('discount', 12, 2)->default(0);
        $table->decimal('tax_rate', 5, 2)->default(0);
        $table->decimal('line_total', 12, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
