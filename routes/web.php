<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierPaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('products', ProductController::class)->except('show');
    Route::resource('contacts', ContactController::class);

    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/payments', [InvoicePaymentController::class, 'store'])->name('invoices.payments.store');
    Route::delete('invoices/{invoice}/payments/{payment}', [InvoicePaymentController::class, 'destroy'])->name('invoices.payments.destroy');

    Route::resource('supplier-payments', SupplierPaymentController::class)->only(['index', 'create', 'store', 'destroy']);

    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('sales', 'sales')->name('sales');
        Route::get('receivables', 'receivables')->name('receivables');
        Route::get('payables', 'payables')->name('payables');
        Route::get('dues', 'dues')->name('dues');
    });

    Route::middleware('can:admin')->group(function () {
        Route::get('settings', [BusinessSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [BusinessSettingController::class, 'update'])->name('settings.update');

        Route::resource('users', UserController::class)->except('show');
    });
});
