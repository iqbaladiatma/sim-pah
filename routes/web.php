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
    // Lembaga Items (Accessible only by Lembaga)
    Route::resource('items', \App\Http\Controllers\ItemController::class)
        ->only(['index', 'update'])
        ->middleware('role:lembaga');

    // Lembaga Requests (Accessible only by Lembaga)
    Route::resource('requests', \App\Http\Controllers\RequestController::class)
        ->only(['index', 'store', 'create'])
        ->middleware('role:lembaga');

    // Lembaga Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Karyawan\DashboardController::class , 'index'])
        ->middleware('role:lembaga')
        ->name('dashboard');

    // Admin Routes
    Route::middleware('admin.access')->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class , 'index'])->name('dashboard');

            // Institutions
            Route::post('/institutions/import', [\App\Http\Controllers\Admin\InstitutionController::class , 'import'])->name('institutions.import');
            Route::get('/institutions/template', [\App\Http\Controllers\Admin\InstitutionController::class , 'downloadTemplate'])->name('institutions.template');
            Route::resource('institutions', \App\Http\Controllers\Admin\InstitutionController::class);

            // Items / Inventory
            Route::post('/items/import', [\App\Http\Controllers\Admin\ItemController::class , 'import'])->name('items.import');
            Route::get('/items/template', [\App\Http\Controllers\Admin\ItemController::class , 'downloadTemplate'])->name('items.template');
            Route::resource('items', \App\Http\Controllers\Admin\ItemController::class);

            // Rooms
            Route::get('/rooms-data/{institution}', [\App\Http\Controllers\Admin\RoomController::class , 'getByInstitution'])->name('rooms.by_institution');
            Route::post('/rooms/import', [\App\Http\Controllers\Admin\RoomController::class , 'import'])->name('rooms.import');
            Route::get('/rooms/template', [\App\Http\Controllers\Admin\RoomController::class , 'downloadTemplate'])->name('rooms.template');
            Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class);

            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

            Route::get('/item-requests', [\App\Http\Controllers\Admin\ItemRequestController::class , 'index'])->name('item_requests.index');
            Route::post('/item-requests/{itemUpdateRequest}/approve', [\App\Http\Controllers\Admin\ItemRequestController::class , 'approve'])->name('item_requests.approve');
            Route::post('/item-requests/{itemUpdateRequest}/reject', [\App\Http\Controllers\Admin\ItemRequestController::class , 'reject'])->name('item_requests.reject');

            // General Requests Admin Management
            Route::resource('requests', \App\Http\Controllers\Admin\GeneralRequestController::class);
            Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class , 'index'])->name('logs.index');
            Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class , 'index'])->name('reports.index');
        }
        );
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
