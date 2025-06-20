<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DealerAuthController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminGRNController;
use App\Http\Controllers\Admin\AdminDealerController;
use App\Http\Controllers\Admin\AdminPartController;
use App\Http\Controllers\TargetController;




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


//invoice preview
Route::get('/invoices/{invoice_no}/preview', [InvoiceController::class, 'preview']);


// download invoice as PDF
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// routes/web.php
Route::get('/dealer/parts/search', [InvoiceController::class, 'searchDealerParts']);

Route::get('/get-vehicle-models', [InvoiceController::class, 'getVehicleModels']);

//cancel invoice
Route::put('/invoices/{invoice_no}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');




// target
Route::resource('targets', TargetController::class);

});
// Optional: delete invoice
//Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');


Route::get('/dealer/register', [DealerAuthController::class, 'showRegisterForm'])->name('dealer.register.form');
Route::post('/dealer/register', [DealerAuthController::class, 'register'])->name('dealer.register');

Route::get('/dealer/login', [DealerAuthController::class, 'showLoginForm'])->name('dealer.login.form');
Route::post('/dealer/login', [DealerAuthController::class, 'login'])->name('dealer.login');

Route::middleware('auth:dealer')->group(function () {
    Route::get('/dealer/dashboard', [DealerAuthController::class, 'dashboard'])->name('dealer.dashboard');
    Route::get('/dealer/finance-summary', [InvoiceController::class, 'getFinanceSummary']);
    Route::post('/dealer/logout', [DealerAuthController::class, 'logout'])->name('dealer.logout');
    Route::get('/dealer/stock', [GRNController::class, 'viewStock'])->name('dealer.stock');

    Route::get('/grn/{grn}/details', [GRNController::class, 'details'])->name('grn.details');
    Route::post('/grn/store', [GRNController::class, 'store'])->name('grn.store');
    Route::get('/grn/history', [GRNController::class, 'history'])->name('grn.history');
    Route::get('/grn/pdf/{id}', [GRNController::class, 'generatePDF'])->name('grn.pdf');
    Route::get('/grn', [GRNController::class, 'index'])->name('grn.index');
    Route::get('/grn/pdf/{id}', [GRNController::class, 'generatePDF'])->name('grn.pdf');

    Route::get('/grn/export/pdf', 'GRNController@exportPDF')->name('grn.export.pdf');
Route::get('/grn/export/excel', 'GRNController@exportExcel')->name('grn.export.excel');

  // data for dealer dashboard
Route::get('/api/dealer/invoice-count', [InvoiceController::class, 'getInvoiceCountForDealer'])
    ->middleware(['auth']);

    Route::get('/api/dealer/monthly-revenue', [InvoiceController::class, 'getMonthlyRevenue'])
    ->middleware(['auth']);

    Route::get('/api/dealer/latest-invoices', [InvoiceController::class, 'getLatestInvoices'])->middleware(['auth']);
    
    Route::get('/api/dealer/top-selling-parts', [InvoiceController::class, 'getTopSellingParts'])->middleware(['auth']);

    Route::get('/dealer/low-stock', [InvoiceController::class, 'fetchLowStockItems'])->name('dealer.lowStock');

    Route::get('/dealer/target-percentage', [TargetController::class, 'getTargetAchievementPercentageApi'])->middleware('auth:dealer');





});

Route::middleware('auth:admin')->group(function () {
    // List all dealers
    Route::get('/dealers', [DealerController::class, 'index'])->name('admin.index');
    
    // Show individual dealer
    Route::get('/dealers/{dealer}', [DealerController::class, 'show'])->name('admin.show');
});
// Admin routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/dealers/{dealer}/toggle-status', [AdminDealerController::class, 'toggleStatus'])->name('admin.dealers.toggle-status');
        
        // GRN Management
        Route::get('/grns', [AdminGRNController::class, 'index'])->name('admin.grns.index');
        
        // Dealer Management
        Route::get('/dealers', [AdminDealerController::class, 'index'])->name('admin.dealers.index');
        Route::delete('/dealers/{dealer}', [AdminDealerController::class, 'destroy'])->name('admin.dealers.destroy');
        Route::put('/dealers/{dealer}', [AdminDealerController::class, 'update'])->name('admin.dealers.update');
        
        // Parts Management
        Route::get('/parts', [AdminPartController::class, 'index'])->name('admin.parts.index');
        Route::put('/parts/{part}', [AdminPartController::class, 'update'])->name('admin.parts.update');

        // Sales Management - Fixed this section
        Route::get('/sales', [\App\Http\Controllers\Admin\AdminSalesController::class, 'index'])->name('admin.sales.index');
        Route::get('/sales/export', [\App\Http\Controllers\Admin\AdminSalesController::class, 'export'])->name('admin.sales.export');
        
        // Login Sessions
        Route::get('/login-sessions', [AdminController::class, 'loginSessions'])->name('admin.login-sessions');
    
});

    // Invoice routes




});