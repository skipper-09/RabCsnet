<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\ItemTypeController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Project\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});



Route::prefix('admin')->group(function () {
    Route::get('', function () {
        return redirect()->route('dashboard');
    });

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');



    Route::prefix('project')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('project');
        Route::get('getData', [ProjectController::class, 'getData'])->name('project.getdata');
        Route::get('/tambah', [ProjectController::class, 'create'])->name('project.add');
        Route::post('store', [ProjectController::class, 'store'])->name('project.store');
        Route::get('/edit/{id}', [ProjectController::class, 'show'])->name('project.edit');
        Route::put('/update/{id}', [ProjectController::class, 'update'])->name('project.update');
        Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->name('project.delete');
    });

    Route::prefix('master')->group(function () {
        Route::prefix('item-type')->group(function () {
            Route::get('/', [ItemTypeController::class, 'index'])->name('itemtype');
            Route::get('getData', [ItemTypeController::class, 'getData'])->name('itemtype.getdata');
            Route::get('/tambah', [ItemTypeController::class, 'create'])->name('itemtype.add');
            Route::post('store', [ItemTypeController::class, 'store'])->name('itemtype.store');
            Route::get('/edit/{id}', [ItemTypeController::class, 'show'])->name('itemtype.edit');
            Route::put('/update/{id}', [ItemTypeController::class, 'update'])->name('itemtype.update');
            Route::delete('/delete/{id}', [ItemTypeController::class, 'destroy'])->name('itemtype.delete');
        });

        //route company master
        Route::prefix('company')->group(function () {
            Route::get('/', [CompanyController::class, 'index'])->name('company');
            Route::get('getData', [CompanyController::class, 'getData'])->name('company.getdata');
            Route::get('/tambah', [CompanyController::class, 'create'])->name('company.add');
            Route::post('store', [CompanyController::class, 'store'])->name('company.store');
            Route::get('/edit/{id}', [CompanyController::class, 'show'])->name('company.edit');
            Route::put('/update/{id}', [CompanyController::class, 'update'])->name('company.update');
            Route::delete('/delete/{id}', [CompanyController::class, 'destroy'])->name('company.delete');
        });

        Route::prefix('unit')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('unit');
            Route::get('getData', [UnitController::class, 'getData'])->name('unit.getdata');
            Route::get('/tambah', [UnitController::class, 'create'])->name('unit.add');
            Route::post('store', [UnitController::class, 'store'])->name('unit.store');
            Route::get('/edit/{id}', [UnitController::class, 'show'])->name('unit.edit');
            Route::put('/update/{id}', [UnitController::class, 'update'])->name('unit.update');
            Route::delete('/delete/{id}', [UnitController::class, 'destroy'])->name('unit.delete');
        });

        Route::prefix('item')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('item');
            Route::get('getData', [ItemController::class, 'getData'])->name('item.getdata');
            Route::get('/tambah', [ItemController::class, 'create'])->name('item.add');
            Route::post('store', [ItemController::class, 'store'])->name('item.store');
            Route::get('/edit/{id}', [ItemController::class, 'show'])->name('item.edit');
            Route::put('/update/{id}', [ItemController::class, 'update'])->name('item.update');
            Route::delete('/delete/{id}', [ItemController::class, 'destroy'])->name('item.delete');
        });
    });
});
