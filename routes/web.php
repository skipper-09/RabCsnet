<?php

use App\Http\Controllers\Dashboard\DashboardController;
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
});
