<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('products', ProductController::class);
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::post('/stock/adjust', [StockController::class, 'adjust'])->name('stock.adjust');

    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);

    Route::resource('leads', LeadController::class);
    Route::post('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/leads/{lead}/activities', [LeadController::class, 'addActivity'])->name('leads.activities');
    Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::post('/sales/{sale}/payment', [SaleController::class, 'recordPayment'])->name('sales.payment');

    Route::resource('purchases', PurchaseController::class);
    Route::post('/purchases/{purchase}/payment', [PurchaseController::class, 'recordPayment'])->name('purchases.payment');

    Route::resource('departments', DepartmentController::class)->except('show');
    Route::resource('employees', EmployeeController::class);
    Route::resource('attendance', AttendanceController::class)->except('show', 'edit', 'update');
    Route::post('/attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');

    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('/payslips/generate', [PayslipController::class, 'generate'])->name('payslips.generate');
    Route::get('/payslips/{payslip}/print', [PayslipController::class, 'print'])->name('payslips.print');
    Route::post('/payslips/{payslip}/paid', [PayslipController::class, 'markPaid'])->name('payslips.mark-paid');

    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/payroll', [ReportController::class, 'payroll'])->name('reports.payroll');

    Route::get('/accounting', [AccountingController::class, 'dashboard'])->name('accounting.dashboard');
    Route::get('/accounting/accounts', [AccountingController::class, 'accounts'])->name('accounting.accounts');
    Route::post('/accounting/accounts', [AccountingController::class, 'storeAccount'])->name('accounting.accounts.store');
    Route::put('/accounting/accounts/{account}', [AccountingController::class, 'updateAccount'])->name('accounting.accounts.update');
    Route::get('/accounting/journal', [AccountingController::class, 'journal'])->name('accounting.journal');
    Route::get('/accounting/journal/create', [AccountingController::class, 'journalCreate'])->name('accounting.journal.create');
    Route::post('/accounting/journal', [AccountingController::class, 'journalStore'])->name('accounting.journal.store');
    Route::get('/accounting/journal/{entry}', [AccountingController::class, 'journalShow'])->name('accounting.journal.show');
    Route::get('/accounting/trial-balance', [AccountingController::class, 'trialBalance'])->name('accounting.trial-balance');
    Route::get('/accounting/income-statement', [AccountingController::class, 'incomeStatement'])->name('accounting.income-statement');
    Route::get('/accounting/balance-sheet', [AccountingController::class, 'balanceSheet'])->name('accounting.balance-sheet');
    Route::get('/accounting/ledger/{account}', [AccountingController::class, 'ledger'])->name('accounting.ledger');

    Route::get('/billing/settings', [BillingController::class, 'settings'])->name('billing.settings');
    Route::post('/billing/settings', [BillingController::class, 'save'])->name('billing.settings.save');
});
