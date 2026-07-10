<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('v1')->group(function () {
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])
        ->middleware('permission:dashboard.view');

    Route::middleware('permission:leads.view')->prefix('leads')->group(function () {
        Route::get('/datatable', [LeadController::class, 'datatable']);
        Route::post('/', [LeadController::class, 'store'])->middleware('permission:leads.create');
        Route::put('/{lead}', [LeadController::class, 'update'])->middleware('permission:leads.edit');
        Route::delete('/{lead}', [LeadController::class, 'destroy'])->middleware('permission:leads.delete');
        Route::post('/{lead}/assign', [LeadController::class, 'assign'])->middleware('permission:leads.assign');
    });

    Route::middleware('permission:customers.view')->prefix('customers')->group(function () {
        Route::get('/datatable', [CustomerController::class, 'datatable']);
        Route::post('/', [CustomerController::class, 'store'])->middleware('permission:customers.create');
        Route::put('/{customer}', [CustomerController::class, 'update'])->middleware('permission:customers.edit');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->middleware('permission:customers.delete');
    });
});
