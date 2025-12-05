<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;

// Root redirect
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// ==========================
// GUEST ROUTES
// ==========================
Route::middleware('guest')->group(function () {

    // Registration (first user only)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    // Password reset
    Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])->name('forgot.password');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetCode'])->name('forgot.password.send');
    Route::get('/reset-password', [PasswordController::class, 'showResetForm'])->name('password.reset.code.form');
    Route::post('/reset-password', [PasswordController::class, 'updatePassword'])->name('password.reset.code.update');
});

// ==========================
// AUTHENTICATED ROUTES
// ==========================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('LendingTracker.Dashboard'))->name('dashboard');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Residents CRUD
    Route::prefix('residents')->name('residents.')->group(function () {
        Route::get('/', [ResidentController::class, 'index'])->name('index');
        Route::post('/', [ResidentController::class, 'store'])->name('store');
        Route::put('/{resident}', [ResidentController::class, 'update'])->name('update');
        Route::delete('/{resident}', [ResidentController::class, 'destroy'])->name('destroy');
    });

    // Items CRUD
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::post('/', [ItemController::class, 'store'])->name('store');
        Route::put('/{item}', [ItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');
    });

    // Borrowing
    Route::prefix('borrowing')->name('borrowing.')->group(function () {
        Route::get('/', [BorrowingController::class, 'index'])->name('index');
        Route::get('/create', [BorrowingController::class, 'create'])->name('create');
        Route::post('/', [BorrowingController::class, 'store'])->name('store');
        Route::post('/{borrowing}/return', [BorrowingController::class, 'markReturned'])->name('return');
    });

    // Reports
    Route::get('/reports', fn() => view('LendingTracker.Reports'))->name('reports.index');

    // Return Tracking
    Route::get('/return-tracking', fn() => view('LendingTracker.ReturnTracking'))->name('return-tracking.index');

    // Archive
    Route::prefix('archive')->name('archive.')->group(function () {
        Route::get('/', [ArchiveController::class, 'index'])->name('index');
        Route::put('/{id}/restore', [ArchiveController::class, 'restore'])->name('restore');
        Route::delete('/{id}', [ArchiveController::class, 'destroy'])->name('destroy');
    });
});
