<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/api/dashboard/stats', [DashboardController::class, 'stats'])
        ->middleware('permission:dashboard.view')
        ->name('api.dashboard.stats');

    Route::prefix('leads')->name('leads.')->middleware('permission:leads.view')->group(function () {
        Route::get('/', [LeadController::class, 'index'])->name('index');
        Route::get('/datatable', [LeadController::class, 'datatable'])->name('datatable');
        Route::post('/sync-indiamart', [LeadController::class, 'syncIndiaMart'])
            ->middleware('permission:leads.create')
            ->name('sync-indiamart');
        Route::get('/create', [LeadController::class, 'create'])->middleware('permission:leads.create')->name('create');
        Route::post('/', [LeadController::class, 'store'])->middleware('permission:leads.create')->name('store');
        Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
        Route::get('/{lead}/edit', [LeadController::class, 'edit'])->middleware('permission:leads.edit')->name('edit');
        Route::put('/{lead}', [LeadController::class, 'update'])->middleware('permission:leads.edit')->name('update');
        Route::delete('/{lead}', [LeadController::class, 'destroy'])->middleware('permission:leads.delete')->name('destroy');
        Route::post('/{lead}/assign', [LeadController::class, 'assign'])->middleware('permission:leads.assign')->name('assign');
    });

    Route::prefix('customers')->name('customers.')->middleware('permission:customers.view')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/datatable', [CustomerController::class, 'datatable'])->name('datatable');
        Route::get('/create', [CustomerController::class, 'create'])->middleware('permission:customers.create')->name('create');
        Route::post('/', [CustomerController::class, 'store'])->middleware('permission:customers.create')->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->middleware('permission:customers.edit')->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.edit')->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete')->name('destroy');
    });

    Route::prefix('companies')->name('companies.')->middleware('permission:companies.view')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/datatable', [CompanyController::class, 'datatable'])->name('datatable');
        Route::post('/', [CompanyController::class, 'store'])->middleware('permission:companies.create')->name('store');
        Route::put('/{company}', [CompanyController::class, 'update'])->middleware('permission:companies.edit')->name('update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->middleware('permission:companies.delete')->name('destroy');
    });

    Route::prefix('products')->name('products.')->middleware('permission:products.view')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/datatable', [ProductController::class, 'datatable'])->name('datatable');
        Route::post('/', [ProductController::class, 'store'])->middleware('permission:products.create')->name('store');
        Route::put('/{product}', [ProductController::class, 'update'])->middleware('permission:products.edit')->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('permission:products.delete')->name('destroy');
    });

    Route::prefix('quotations')->name('quotations.')->middleware('permission:quotations.view')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/datatable', [QuotationController::class, 'datatable'])->name('datatable');
        Route::post('/', [QuotationController::class, 'store'])->middleware('permission:quotations.create')->name('store');
        Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->middleware('permission:quotations.delete')->name('destroy');
    });

    Route::prefix('tasks')->name('tasks.')->middleware('permission:tasks.view')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/datatable', [TaskController::class, 'datatable'])->name('datatable');
        Route::post('/', [TaskController::class, 'store'])->middleware('permission:tasks.create')->name('store');
        Route::put('/{task}', [TaskController::class, 'update'])->middleware('permission:tasks.edit')->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->middleware('permission:tasks.delete')->name('destroy');
    });

    Route::prefix('users')->name('users.')->middleware('permission:users.view')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/datatable', [UserController::class, 'datatable'])->name('datatable');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:users.edit')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
    });

    Route::prefix('roles')->name('roles.')->middleware('permission:roles.view')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/datatable', [RoleController::class, 'datatable'])->name('datatable');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create')->name('store');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('destroy');
    });

    Route::prefix('permissions')->name('permissions.')->middleware('permission:permissions.view')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('/datatable', [PermissionController::class, 'datatable'])->name('datatable');
        Route::post('/sync', [PermissionController::class, 'sync'])->middleware('permission:permissions.edit')->name('sync');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
