<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;

// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Auth routes with email verification
Auth::routes(['verify' => true]);

// Home route after login
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Protected routes (only for authenticated and verified users)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Resources
    Route::resource('categories', CategoryController::class);
    Route::resource('accounts', AccountController::class);
    Route::resource('transactions', TransactionController::class);
    Route::resource('budgets', BudgetController::class);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/spending-by-category', [ReportController::class, 'spendingByCategory'])->name('reports.spending-by-category');
    Route::get('/reports/income-vs-expense', [ReportController::class, 'incomeVsExpense'])->name('reports.income-vs-expense');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [SettingsController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [SettingsController::class, 'update'])->name('profile.update');
});
