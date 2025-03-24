<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

// Landing page (optional)
Route::get('/', function () {
    return redirect()->route('invoices.create');
});

// Invoice routes
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');

// Optional: view a single invoice
//Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

Route::get('/invoices/{invoice_no}/preview', [InvoiceController::class, 'preview']);


// Optional: download invoice as PDF
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');


// Optional: delete invoice
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
