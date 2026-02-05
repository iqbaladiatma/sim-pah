<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
    'canLogin' => Route::has('login'),
    'canRegister' => Route::has('register'),
    'laravelVersion' => Application::VERSION,
    'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('items', \App\Http\Controllers\ItemController::class);
    Route::resource('requests', \App\Http\Controllers\RequestController::class)->only(['index', 'store']);
    // Karyawan Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Karyawan\DashboardController::class , 'index'])
        ->middleware('role:karyawan')
        ->name('dashboard');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class , 'index'])->name('dashboard');
            Route::resource('institutions', \App\Http\Controllers\Admin\InstitutionController::class);
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
            Route::get('/item-requests', [\App\Http\Controllers\Admin\ItemRequestController::class , 'index'])->name('item_requests.index');
            Route::post('/item-requests/{itemUpdateRequest}/approve', [\App\Http\Controllers\Admin\ItemRequestController::class , 'approve'])->name('item_requests.approve');
            Route::post('/item-requests/{itemUpdateRequest}/reject', [\App\Http\Controllers\Admin\ItemRequestController::class , 'reject'])->name('item_requests.reject');
            // General Requests Admin Update
            Route::put('/requests/{generalRequest}', [\App\Http\Controllers\RequestController::class , 'update'])->name('requests.update');
        }
        );
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
