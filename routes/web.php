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
use App\Http\Controllers\Report\ReportVendorController;
use App\Http\Controllers\Settings\SettingAplicationController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\PaymentVendor\PaymentVendorController;
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

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('can:read-dashboard');


//route project
    Route::prefix('project')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('project');
        Route::get('getData', [ProjectController::class, 'getData'])->name('project.getdata');
        Route::get('/tambah', [ProjectController::class, 'create'])->name('project.add');
        Route::post('store', [ProjectController::class, 'store'])->name('project.store');
        Route::get('/edit/{id}', [ProjectController::class, 'show'])->name('project.edit');
        // Route::get('/detail/{id}', [ProjectController::class, 'detail'])->name('project.detail');
        Route::get('/edit/{id}', [ProjectController::class, 'show'])->name('project.edit');
        Route::put('/update/{id}', [ProjectController::class, 'update'])->name('project.update');
        Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->name('project.delete');
        Route::get('/proses/{id}', [ProjectController::class, 'ProsesProject'])->name('project.proses');
    });

    Route::prefix('detail/{id}')->group(function () {
        Route::get('/', [ProjectController::class, 'detail'])->name('project.detail');
        Route::get('getData', [DetailProjectController::class, 'getData'])->name('projectdetail.getdata');
        Route::get('/tambah', [DetailProjectController::class, 'create'])->name('projectdetail.add');
        Route::post('store', [DetailProjectController::class, 'store'])->name('projectdetail.store');
        Route::get('/edit/{iddetail}', [DetailProjectController::class, 'show'])->name('projectdetail.edit');
        // Route::get('/detail/{id}', [DetailProjectController::class, 'detail'])->name('projectdetail.detail');
        Route::put('/update/{iddetail}', [DetailProjectController::class, 'update'])->name('projectdetail.update');
        Route::delete('/delete/{iddetail}', [DetailProjectController::class, 'destroy'])->name('projectdetail.delete');
    });

    //route vendor
    Route::prefix('vendor')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->name('vendor')->middleware('can:read-vendors');
        Route::get('getData', [VendorController::class, 'getData'])->name('vendor.getdata');
        Route::get('/tambah', [VendorController::class, 'create'])->name('vendor.add')->middleware('can:create-vendors');
        Route::post('store', [VendorController::class, 'store'])->name('vendor.store');
        Route::get('/edit/{id}', [VendorController::class, 'show'])->name('vendor.edit')->middleware('can:update-vendors');
        Route::put('/update/{id}', [VendorController::class, 'update'])->name('vendor.update');
        Route::delete('/delete/{id}', [VendorController::class, 'destroy'])->name('vendor.delete')->middleware('can:delete-vendors');
    });

    //route payment vendor
    Route::prefix('payment')->group(function () {
        Route::get('/', [PaymentVendorController::class, 'index'])->name('payment')->middleware('can:read-paymentvendors');
        Route::get('getData', [PaymentVendorController::class, 'getData'])->name('payment.getdata');
        Route::get('/tambah', [PaymentVendorController::class, 'create'])->name('payment.add')->middleware('can:create-paymentvendors');
        Route::post('store', [PaymentVendorController::class, 'store'])->name('payment.store');
        Route::get('/edit/{id}', [PaymentVendorController::class, 'show'])->name('payment.edit')->middleware('can:update-paymentvendors');
        Route::put('/update/{id}', [PaymentVendorController::class, 'update'])->name('payment.update');
        Route::delete('/delete/{id}', [PaymentVendorController::class, 'destroy'])->name('payment.delete')->middleware('can:delete-paymentvendors');
    });

    //route master group
    Route::prefix('master')->group(function () {
        Route::prefix('item-type')->group(function () {
            Route::get('/', [ItemTypeController::class, 'index'])->name('itemtype')->middleware('can:read-itemtypes');
            Route::get('getData', [ItemTypeController::class, 'getData'])->name('itemtype.getdata');
            Route::get('/tambah', [ItemTypeController::class, 'create'])->name('itemtype.add')->middleware('can:create-itemtypes');
            Route::post('store', [ItemTypeController::class, 'store'])->name('itemtype.store');
            Route::get('/edit/{id}', [ItemTypeController::class, 'show'])->name('itemtype.edit')->middleware('can:update-itemtypes');
            Route::put('/update/{id}', [ItemTypeController::class, 'update'])->name('itemtype.update');
            Route::delete('/delete/{id}', [ItemTypeController::class, 'destroy'])->name('itemtype.delete')->middleware('can:delete-itemtypes');
        });

        //route company master
        Route::prefix('company')->group(function () {
            Route::get('/', [CompanyController::class, 'index'])->name('company')->middleware('can:read-companies');
            Route::get('getData', [CompanyController::class, 'getData'])->name('company.getdata');
            Route::get('/tambah', [CompanyController::class, 'create'])->name('company.add')->middleware('can:create-companies');
            Route::post('store', [CompanyController::class, 'store'])->name('company.store');
            Route::get('/edit/{id}', [CompanyController::class, 'show'])->name('company.edit')->middleware('can:update-companies');
            Route::put('/update/{id}', [CompanyController::class, 'update'])->name('company.update');
            Route::delete('/delete/{id}', [CompanyController::class, 'destroy'])->name('company.delete')->middleware('can:delete-companies');
        });

        //route unit
        Route::prefix('unit')->group(function () {
            Route::get('/', [UnitController::class, 'index'])->name('unit')->middleware('can:read-units');
            Route::get('getData', [UnitController::class, 'getData'])->name('unit.getdata');
            Route::get('/tambah', [UnitController::class, 'create'])->name('unit.add')->middleware('can:create-units');
            Route::post('store', [UnitController::class, 'store'])->name('unit.store');
            Route::get('/edit/{id}', [UnitController::class, 'show'])->name('unit.edit')->middleware('can:update-units');
            Route::put('/update/{id}', [UnitController::class, 'update'])->name('unit.update'); 
            Route::delete('/delete/{id}', [UnitController::class, 'destroy'])->name('unit.delete')->middleware('can:delete-units');
        });

        //route item
        Route::prefix('item')->group(function () {
            Route::get('/', [ItemController::class, 'index'])->name('item')->middleware('can:read-items');
            Route::get('getData', [ItemController::class, 'getData'])->name('item.getdata');
            Route::get('/tambah', [ItemController::class, 'create'])->name('item.add')->middleware('can:create-items');
            Route::post('store', [ItemController::class, 'store'])->name('item.store');
            Route::get('/edit/{id}', [ItemController::class, 'show'])->name('item.edit')->middleware('can:update-items');
            Route::put('/update/{id}', [ItemController::class, 'update'])->name('item.update');
            Route::delete('/delete/{id}', [ItemController::class, 'destroy'])->name('item.delete')->middleware('can:delete-items');
        });

        //route project type
        Route::prefix('project-type')->group(function () {
            Route::get('/', [ProjectTypeController::class, 'index'])->name('projecttype')->middleware('can:read-projecttypes');
            Route::get('getData', [ProjectTypeController::class, 'getData'])->name('projecttype.getdata');
            Route::get('/tambah', [ProjectTypeController::class, 'create'])->name('projecttype.add')->middleware('can:create-projecttypes');
            Route::post('store', [ProjectTypeController::class, 'store'])->name('projecttype.store');
            Route::get('/edit/{id}', [ProjectTypeController::class, 'show'])->name('projecttype.edit')->middleware('can:update-projecttypes');
            Route::put('/update/{id}', [ProjectTypeController::class, 'update'])->name('projecttype.update');
            Route::delete('/delete/{id}', [ProjectTypeController::class, 'destroy'])->name('projecttype.delete')->middleware('can:delete-projecttypes');
        });
    });

    Route::prefix('report')->group(function () {
        Route::get('/', [ReportVendorController::class, 'index'])->name('report');
        Route::get('getData', [ReportVendorController::class, 'getData'])->name('report.getdata');
        Route::get('/tambah', [ReportVendorController::class, 'create'])->name('report.add');
        Route::post('store', [ReportVendorController::class, 'store'])->name('report.store');
        Route::get('/edit/{id}', [ReportVendorController::class, 'show'])->name('report.edit');
        Route::put('/update/{id}', [ReportVendorController::class, 'update'])->name('report.update');
        Route::delete('/delete/{id}', [ReportVendorController::class, 'destroy'])->name('report.delete');
    });

    Route::prefix('settings')->group(function () {

        Route::prefix('role')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('role')->middleware('can:read-roles');
            Route::get('getData', [RoleController::class, 'getData'])->name('role.getdata');
            Route::get('/tambah', [RoleController::class, 'create'])->name('role.add')->middleware('can:create-roles');
            Route::post('store', [RoleController::class, 'store'])->name('role.store');
            Route::get('/edit/{id}', [RoleController::class, 'show'])->name('role.edit')->middleware('can:update-roles');
            Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update');
            Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete')->middleware('can:delete-roles');
        });

        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user')->middleware('can:read-users');
            Route::get('getData', [UserController::class, 'getData'])->name('user.getdata');
            Route::get('/tambah', [UserController::class, 'create'])->name('user.add')->middleware('can:create-users');
            Route::post('store', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'show'])->name('user.edit')->middleware('can:update-users');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.delete')->middleware('can:delete-users');
        });

        //setting aplication
        Route::prefix('application')->group(function () {
            Route::get('/', [SettingAplicationController::class, 'index'])->name('aplication');
            Route::post('/update', [SettingAplicationController::class, 'update'])->name('aplication.update');
        });

        // Log Activity
        Route::prefix('log')->group(function () {
            Route::get('/', [LogController::class, 'index'])->name('log')-> middleware('can:read-logs');
            Route::get('getData', [LogController::class, 'getData'])->name('log.getdata');
            Route::post('clean', [LogController::class, 'cleanlog'])->name('log.clean')->middleware('can:clean-logs');
        });
    });
});
