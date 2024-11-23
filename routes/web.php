<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\ItemTypeController;
use App\Http\Controllers\Master\ProjectTypeController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Project\DetailProjectController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Settings\SettingAplicationController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\LogController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('auth')->group(function () {
    Route::get('login', [AuthController::class, 'index'])->name('login')->middleware('guest');
    Route::post('signin', [AuthController::class, 'signin'])->name('auth.signin');
    Route::get('signout', [AuthController::class, 'signout'])->name('auth.signout');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('', function () {
        return redirect()->route('dashboard');
    });

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


//route project
    Route::prefix('project')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('project');
        Route::get('getData', [ProjectController::class, 'getData'])->name('project.getdata');
        Route::get('/tambah', [ProjectController::class, 'create'])->name('project.add');
        Route::post('store', [ProjectController::class, 'store'])->name('project.store');
        Route::get('/edit/{id}', [ProjectController::class, 'show'])->name('project.edit');
        // Route::get('/detail/{id}', [ProjectController::class, 'detail'])->name('project.detail');
        Route::put('/update/{id}', [ProjectController::class, 'update'])->name('project.update');
        Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->name('project.delete');
    });

    Route::prefix('detail/{id}')->group(function () {
        Route::get('/', [ProjectController::class, 'detail'])->name('project.detail');
        Route::get('getData', [DetailProjectController::class, 'getData'])->name('projectdetail.getdata');
        Route::get('/tambah', [DetailProjectController::class, 'create'])->name('projectdetail.add');
        Route::post('store', [DetailProjectController::class, 'store'])->name('projectdetail.store');
        // Route::get('/edit/{id}', [DetailProjectController::class, 'show'])->name('projectdetail.edit');
        // Route::get('/detail/{id}', [DetailProjectController::class, 'detail'])->name('projectdetail.detail');
        // Route::put('/update/{id}', [DetailProjectController::class, 'update'])->name('projectdetail.update');
        Route::delete('/delete/{iddetail}', [DetailProjectController::class, 'destroy'])->name('projectdetail.delete');
    });

    //route vendor
    Route::prefix('vendor')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendor');
        Route::get('getData', [VendorController::class, 'getData'])->name('vendor.getdata');
        Route::get('/tambah', [VendorController::class, 'create'])->name('vendor.add');
        Route::post('store', [VendorController::class, 'store'])->name('vendor.store');
        Route::get('/edit/{id}', [VendorController::class, 'show'])->name('vendor.edit');
        Route::put('/update/{id}', [VendorController::class, 'update'])->name('vendor.update');
        Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('vendor.delete');
    });

    //route master group
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

        //route unit
        Route::prefix('unit')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('unit');
            Route::get('getData', [UnitController::class, 'getData'])->name('unit.getdata');
            Route::get('/tambah', [UnitController::class, 'create'])->name('unit.add');
            Route::post('store', [UnitController::class, 'store'])->name('unit.store');
            Route::get('/edit/{id}', [UnitController::class, 'show'])->name('unit.edit');
            Route::put('/update/{id}', [UnitController::class, 'update'])->name('unit.update');
            Route::delete('/delete/{id}', [UnitController::class, 'destroy'])->name('unit.delete');
        });

        //route item
        Route::prefix('item')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('item');
            Route::get('getData', [ItemController::class, 'getData'])->name('item.getdata');
            Route::get('/tambah', [ItemController::class, 'create'])->name('item.add');
            Route::post('store', [ItemController::class, 'store'])->name('item.store');
            Route::get('/edit/{id}', [ItemController::class, 'show'])->name('item.edit');
            Route::put('/update/{id}', [ItemController::class, 'update'])->name('item.update');
            Route::delete('/delete/{id}', [ItemController::class, 'destroy'])->name('item.delete');
        });

        //route project type
        Route::prefix('project-type')->group(function () {
            Route::get('/', [ProjectTypeController::class, 'index'])->name('projecttype');
            Route::get('getData', [ProjectTypeController::class, 'getData'])->name('projecttype.getdata');
            Route::get('/tambah', [ProjectTypeController::class, 'create'])->name('projecttype.add');
            Route::post('store', [ProjectTypeController::class, 'store'])->name('projecttype.store');
            Route::get('/edit/{id}', [ProjectTypeController::class, 'show'])->name('projecttype.edit');
            Route::put('/update/{id}', [ProjectTypeController::class, 'update'])->name('projecttype.update');
            Route::delete('/delete/{id}', [ProjectTypeController::class, 'destroy'])->name('projecttype.delete');
        });
    });

    Route::prefix('settings')->group(function () {

        Route::prefix('role')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('role');
            Route::get('getData', [RoleController::class, 'getData'])->name('role.getdata');
            Route::get('/tambah', [RoleController::class, 'create'])->name('role.add');
            Route::post('store', [RoleController::class, 'store'])->name('role.store');
            Route::get('/edit/{id}', [RoleController::class, 'show'])->name('role.edit');
            Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update');
            Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete');
        });

        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user');
            Route::get('getData', [UserController::class, 'getData'])->name('user.getdata');
            Route::get('/tambah', [UserController::class, 'create'])->name('user.add');
            Route::post('store', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'show'])->name('user.edit');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
        });

        //setting aplication
        Route::prefix('application')->group(function () {
            Route::get('/{id}', [SettingAplicationController::class, 'index'])->name('aplication');
            Route::put('/update/{id}', [SettingAplicationController::class, 'update'])->name('aplication.update');
        });

        // Log Activity
        Route::prefix('log')->group(function () {
            Route::get('/', [LogController::class, 'index'])->name('log');
            Route::get('getData', [LogController::class, 'getData'])->name('log.getdata');
            Route::post('clean', [LogController::class, 'cleanlog'])->name('log.clean');
        });
    });
});
