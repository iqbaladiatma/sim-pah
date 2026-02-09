<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Items (Accessible by all, role logic in controller)
    // Note: Admin routes are prefixed 'admin.' so no name collision.
    Route::resource('items', \App\Http\Controllers\ItemController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Lembaga Requests (Accessible only by Lembaga)
    Route::resource('requests', \App\Http\Controllers\RequestController::class)
        ->only(['index', 'store', 'create', 'edit', 'update', 'destroy'])
        ->middleware('role:lembaga');

    // Lembaga Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Karyawan\DashboardController::class , 'index'])
        ->middleware('role:lembaga')
        ->name('dashboard');

    // Admin Routes
    Route::middleware('admin.access')->prefix('admin')->name('admin.')->group(
        function () {
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
            Route::get('/rooms/import', [\App\Http\Controllers\Admin\RoomController::class , 'showImport'])->name('rooms.import_page');
            Route::post('/rooms/import', [\App\Http\Controllers\Admin\RoomController::class , 'import'])->name('rooms.import');
            Route::get('/rooms/template', [\App\Http\Controllers\Admin\RoomController::class , 'downloadTemplate'])->name('rooms.template');
            Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class);

            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

            Route::get('/item-requests', [\App\Http\Controllers\Admin\ItemRequestController::class , 'index'])->name('item_requests.index');
            Route::post('/item-requests/{itemUpdateRequest}/approve', [\App\Http\Controllers\Admin\ItemRequestController::class , 'approve'])->name('item_requests.approve');
            Route::post('/item-requests/{itemUpdateRequest}/reject', [\App\Http\Controllers\Admin\ItemRequestController::class , 'reject'])->name('item_requests.reject');

            // General Requests Admin Management
            Route::get('/requests/export', [\App\Http\Controllers\Admin\GeneralRequestController::class , 'export'])->name('requests.export');
            Route::resource('requests', \App\Http\Controllers\Admin\GeneralRequestController::class);

            // Activity Log & Online Users (Super Admin only)
            Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class , 'index'])->name('activity_log.index');
            Route::delete('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class , 'destroy'])->name('activity_log.destroy');
            Route::get('/online-users', [\App\Http\Controllers\Admin\OnlineUsersController::class , 'index'])->name('online_users.index');
            Route::delete('/online-users/{session}', [\App\Http\Controllers\Admin\OnlineUsersController::class , 'kick'])->name('online_users.kick');

            // System Orchestrator (Super Admin only)
            Route::middleware('role:super admin')->group(function () {
                    Route::get('/system-control', [\App\Http\Controllers\Admin\SystemControlController::class , 'index'])->name('system_control.index');
                    Route::patch('/system-control/{setting}', [\App\Http\Controllers\Admin\SystemControlController::class , 'update'])->name('system_control.update');
                    Route::post('/system-control/command', [\App\Http\Controllers\Admin\SystemControlController::class , 'runCommand'])->name('system_control.command');
                    Route::get('/system-control/playground', [\App\Http\Controllers\Admin\PlaygroundController::class , 'index'])->name('system_control.playground');
                    Route::get('/system-control/playground/error/{status}', [\App\Http\Controllers\Admin\PlaygroundController::class , 'showError'])->name('system_control.playground.error');
                }
                );

                Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class , 'index'])->name('reports.index');

                // URT ISO Procedures
                Route::prefix('procedures')->name('procedures.')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'index'])->name('index');
                    Route::get('/dashboard', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'dashboard'])->name('dashboard');

                    // Excel Import/Export
                    Route::get('/export-all', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'exportAll'])->name('export_all');
                    Route::get('/export/{type}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'export'])->name('export');
                    Route::post('/check-import-headers', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'checkImportHeaders'])->name('check_import_headers');
                    Route::post('/import/{type}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'import'])->name('import');

                    // Specific helpers
                    Route::get('/template/{type}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'downloadTemplate'])->name('template');

                    // Generic Procedures (MUST BE LAST)
                    Route::get('/{type}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'show'])->name('show');
                    Route::post('/store/{type}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'store'])->name('store');
                    Route::put('/update/{type}/{id}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'update'])->name('update');
                    Route::delete('/destroy/{type}/{id}', [\App\Http\Controllers\Admin\UrtProcedureController::class , 'destroy'])->name('destroy');
                }
                );
            }
            );        });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class , 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
