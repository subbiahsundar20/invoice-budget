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
       Schema::create('purchase_invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('supplier_id')
            ->constrained('suppliers')->restrictOnDelete();
        $table->string('invoice_number')->unique();
        $table->date('invoice_date');
        $table->date('due_date');
        $table->decimal('subtotal', 12, 2)->default(0);
        $table->decimal('discount', 12, 2)->default(0);
        $table->decimal('tax', 12, 2)->default(0);
        $table->decimal('total', 12, 2)->default(0);
        $table->string('status')->default('pending');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
