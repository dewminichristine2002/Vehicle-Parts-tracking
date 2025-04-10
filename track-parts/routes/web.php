<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DealerAuthController;
use App\Http\Controllers\GRNController;

// Landing page (optional)
Route::get('/', function () {
    return redirect()->route('dealer.login.form');
});

// Invoice routes
Route::middleware('auth:dealer')->group(function () {
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/autocomplete-invoices', [InvoiceController::class, 'autocomplete']);
Route::get('/contacts/search', [InvoiceController::class, 'searchContacts']);
Route::get('/vehicles/search', [InvoiceController::class, 'searchVehicle']);
Route::get('/other-costs/suggestions', [InvoiceController::class, 'otherCostSuggestions']);

// Customer routes
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

/*
Route::get('/customers/{contact_number}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{contact_number}', [CustomerController::class, 'update'])->name('customers.update');
*/



//invoice preview
Route::get('/invoices/{invoice_no}/preview', [InvoiceController::class, 'preview']);


// download invoice as PDF
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// routes/web.php
Route::get('/dealer/parts/search', [InvoiceController::class, 'searchDealerParts']);


});
// Optional: delete invoice
//Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');


Route::get('/dealer/register', [DealerAuthController::class, 'showRegisterForm'])->name('dealer.register.form');
Route::post('/dealer/register', [DealerAuthController::class, 'register'])->name('dealer.register');

Route::get('/dealer/login', [DealerAuthController::class, 'showLoginForm'])->name('dealer.login.form');
Route::post('/dealer/login', [DealerAuthController::class, 'login'])->name('dealer.login');

Route::middleware('auth:dealer')->group(function () {
    Route::get('/dealer/dashboard', [DealerAuthController::class, 'dashboard'])->name('dealer.dashboard');
    Route::post('/dealer/logout', [DealerAuthController::class, 'logout'])->name('dealer.logout');

    Route::get('/grn/{grn}/details', [GRNController::class, 'details'])->name('grn.details');
    Route::post('/grn/store', [GRNController::class, 'store'])->name('grn.store');
    Route::get('/grn/history', [GRNController::class, 'history'])->name('grn.history');
    Route::get('/grn/pdf/{id}', [GRNController::class, 'generatePDF'])->name('grn.pdf');
    Route::get('/grn', [GRNController::class, 'index'])->name('grn.index');
    Route::get('/grn/pdf/{id}', [GRNController::class, 'generatePDF'])->name('grn.pdf');

    Route::get('/grn/export/pdf', 'GRNController@exportPDF')->name('grn.export.pdf');
Route::get('/grn/export/excel', 'GRNController@exportExcel')->name('grn.export.excel');
});