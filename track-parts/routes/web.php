<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;

// Landing page (optional)
Route::get('/', function () {
    return redirect()->route('invoices.create');
});

// Invoice routes
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/autocomplete-invoices', [InvoiceController::class, 'autocomplete']);
Route::get('/contacts/search', [InvoiceController::class, 'searchContacts']);
Route::get('/vehicles/search', [InvoiceController::class, 'searchVehicle']);
Route::get('/other-costs/suggestions', [InvoiceController::class, 'otherCostSuggestions']);

// Cusatomer routes
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


// Optional: delete invoice
//Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

