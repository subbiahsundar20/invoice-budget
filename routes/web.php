<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;


Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);
      Route::resource(
        'sales-invoices',
        SalesInvoiceController::class
    );

    Route::resource(
        'purchase-invoices',
        PurchaseInvoiceController::class
    );
   Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');

   Route::get('/reports/export', [ReportController::class, 'export'])
    ->name('reports.export');
});

require __DIR__.'/auth.php';
