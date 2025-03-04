<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\ItemTypeController;
use App\Http\Controllers\Master\ProjectTypeController;
use App\Http\Controllers\Master\UnitController;
use App\Http\Controllers\Master\ItemController;
use App\Http\Controllers\Master\ServiceController;
use App\Http\Controllers\PhpInfoController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Project\DetailProjectController;
use App\Http\Controllers\Project\PerijinanProjectController;
use App\Http\Controllers\Project\ProjectController;
use App\Http\Controllers\Report\ProjectReportController;
use App\Http\Controllers\Report\ReportVendorController;
use App\Http\Controllers\Settings\SettingAplicationController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\PaymentVendor\PaymentVendorController;
use App\Http\Controllers\Project\ATPProjectController;
use App\Http\Controllers\Review\ProjectReviewController;
use App\Http\Controllers\Task\TimelineController;
use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\LogController;
use App\Http\Controllers\Task\TaskAssignController;
use App\Http\Controllers\Task\TaskController;
use App\Models\ProjectLisence;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Route::get('/php-info', [PhpInfoController::class, 'showInfo']);

Route::prefix('auth')->group(function () {
    Route::get('login', [AuthController::class, 'index'])->name('login')->middleware('guest');
    Route::get('reset-password', [AuthController::class, 'ResetPassword'])->name('resetpassword')->middleware('guest');
    Route::post('signin', [AuthController::class, 'signin'])->name('auth.signin');
    Route::get('signout', [AuthController::class, 'signout'])->name('auth.signout');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('', function () {
        return redirect()->route('dashboard');
    });

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('can:read-dashboard');
    Route::get('dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data')->middleware('can:read-dashboard');

    //route project
    Route::prefix('project')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('project')->middleware('can:read-projects');
        Route::get('getData', [ProjectController::class, 'getData'])->name('project.getdata');
        Route::get('/tambah', [ProjectController::class, 'create'])->name('project.add')->middleware('can:create-projects');
        Route::post('store', [ProjectController::class, 'store'])->name('project.store');
        Route::get('/edit/{id}', [ProjectController::class, 'show'])->name('project.edit')->middleware('can:update-projects');
        Route::get('/detail/{id}', [ProjectController::class, 'detail'])->name('project.detail')->middleware('can:read-detail-projects');
        // Route::get('/edit/{id}', [ProjectController::class, 'show'])->name('project.edit');
        Route::put('/update/{id}', [ProjectController::class, 'update'])->name('project.update');
        Route::delete('/delete/{id}', [ProjectController::class, 'destroy'])->name('project.delete')->middleware('can:delete-projects');
        Route::get('/proses/{id}', [ProjectController::class, 'ProsesProject'])->name('project.proses')->middleware('can:approval-projects');
        Route::post('/prosesdata/{id}', [ProjectController::class, 'ProsesProjectStore'])->name('project.prosesdata');
        Route::get('/start/{id}', [ProjectController::class, 'StartProject'])->name('project.start')->middleware('can:start-projects');
        Route::put('/prosesstart/{id}', [ProjectController::class, 'ProjectStart'])->name('project.prosesstart');

        Route::get('/{project}/finish', [ProjectController::class, 'finishProject'])
            ->name('project.finish')
            ->middleware('can:finish-projects');
        
        Route::get('/enable-atp-upload/{project}', [ATPProjectController::class, 'enableAtpUpload'])
            ->name('project.enable-atp-upload')
            ->middleware('can:enable-atp-upload');

        Route::get('/disable-atp-upload/{project}', [ATPProjectController::class, 'disableAtpUpload'])
            ->name('project.disable-atp-upload')
            ->middleware('can:disable-atp-upload');

        // Upload ATP File for Vendor
        Route::get('/upload-atp/{project}', [ATPProjectController::class, 'uploadAtpView'])
            ->name('project.upload-atp')
            ->middleware('can:upload-atp');

        Route::post('/upload-atp/{project}', [ATPProjectController::class, 'storeAtpFile'])
            ->name('project.store-atp');

        Route::get('/download-atp/{project}', [AtpProjectController::class, 'downloadAtpFile'])
            ->name('project.download-atp')
            ->middleware('can:download-atp');
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

        //project lisence
        Route::get('getDataLisence', [PerijinanProjectController::class, 'getData'])->name('projectlisence.getdata');
        Route::get('/tambah-perijinan', [PerijinanProjectController::class, 'create'])->name('projectlisence.add');
        Route::post('store-perijinan', [PerijinanProjectController::class, 'store'])->name('projectlisence.store');
        Route::get('/edit-perijinan/{idperijinan}', [PerijinanProjectController::class, 'show'])->name('projectlisence.edit');
        Route::put('/update-perijinan/{idperijinan}', [PerijinanProjectController::class, 'update'])->name('projectlisence.update');
        Route::delete('/delete-perijinan/{idperijinan}', [PerijinanProjectController::class, 'destroy'])->name('projectlisence.delete');
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
            Route::get('/export', [ItemController::class, 'ExportItem'])->name('item.export')->middleware('can:export-items');
            Route::get('/exportpdf', [ItemController::class, 'exportItemsToPdf'])->name('item.exportpdf')->middleware('can:export-items');
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

        Route::prefix('service')->group(function () {
            Route::get('/', [ServiceController::class, 'index'])->name('service')->middleware('can:read-services');
            Route::get('getData', [ServiceController::class, 'getData'])->name('service.getdata');
            Route::get('/tambah', [ServiceController::class, 'create'])->name('service.add')->middleware('can:create-services');
            Route::post('store', [ServiceController::class, 'store'])->name('service.store');
            Route::get('/edit/{id}', [ServiceController::class, 'show'])->name('service.edit')->middleware('can:update-services');
            Route::put('/update/{id}', [ServiceController::class, 'update'])->name('service.update');
            Route::delete('/delete/{id}', [ServiceController::class, 'destroy'])->name('service.delete')->middleware('can:delete-services');
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

        Route::prefix('project')->group(function () {
            Route::get('/', [ProjectReportController::class, 'index'])->name('report.project')->middleware('can:read-report-project');
            Route::get('getDataReview', [ProjectReportController::class, 'getDataReview'])->name('report.project.getdatareview');
            Route::get('getDataDetail', [ProjectReportController::class, 'DetailProjectReport'])->name('report.project.getdetailproject');
            Route::get('getDataDetailItem/{id}', [ProjectReportController::class, 'DetailItem'])->name('report.project.getdetailitem');
        });
    });

    Route::prefix('review')->group(function () {
        Route::get('/', [ProjectReviewController::class, 'index'])->name('review')->middleware('can:read-projectreviews');
        Route::get('getData', [ProjectReviewController::class, 'getData'])->name('review.getdata');
        Route::get('/tambah', [ProjectReviewController::class, 'create'])->name('review.add')->middleware('can:create-projectreviews');
        Route::post('store', [ProjectReviewController::class, 'store'])->name('review.store');
        Route::get('/edit/{id}', [ProjectReviewController::class, 'show'])->name('review.edit')->middleware('can:update-projectreviews');
        Route::put('/update/{id}', [ProjectReviewController::class, 'update'])->name('review.update');
        Route::delete('/delete/{id}', [ProjectReviewController::class, 'destroy'])->name('review.delete')->middleware('can:delete-projectreviews');
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks')->middleware('can:read-tasks');
        Route::get('getData', [TaskController::class, 'getData'])->name('tasks.getdata');
        Route::get('/get-parent-tasks/{projectId}', [TaskController::class, 'getParentTasksByProjectVendor'])
            ->name('tasks.get-parent-tasks');
        Route::get('/detail/{id}', [TaskController::class, 'details'])->name('tasks.details');
        Route::get('/tambah', [TaskController::class, 'create'])->name('tasks.add')->middleware('can:create-tasks');
        Route::post('store', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/edit/{id}', [TaskController::class, 'show'])->name('tasks.edit')->middleware('can:update-tasks');
        Route::put('/update/{id}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->name('tasks.delete')->middleware('can:delete-tasks');
        Route::post('/{id}/toggle-completion', [TaskController::class, 'toggleCompletion'])
            ->name('tasks.toggle-completion')
            ->middleware('can:complete-tasks');
        Route::get('/report/{taskId}', [TaskController::class, 'showReportTask'])->name('tasks.report.show');
        Route::post('/report', [TaskController::class, 'reportTask'])
            ->name('tasks.report');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])
            ->name('tasks.update-status')->middleware('can:update-kanban-tasks');
    });

    Route::prefix('assign')->group(function () {
        Route::get('/', [TaskAssignController::class, 'index'])->name('tasks.assign');
        Route::get('getData', [TaskAssignController::class, 'getData'])->name('tasks.assign.getdata');
        Route::get('/tambah', [TaskAssignController::class, 'create'])->name('tasks.assign.add');
        Route::post('store', [TaskAssignController::class, 'store'])->name('tasks.assign.store');
        Route::get('/edit/{id}', [TaskAssignController::class, 'show'])->name('tasks.assign.edit');
        Route::put('/update/{id}', [TaskAssignController::class, 'update'])->name('tasks.assign.update');
        Route::delete('/delete/{id}', [TaskAssignController::class, 'destroy'])->name('tasks.assign.delete');
        Route::post('/progress/{id}', [TaskAssignController::class, 'updateProgress'])->name('tasks.assign.progress.update');
    });



    Route::prefix('timeline')->group(function () {
        Route::get('/', [TimelineController::class, 'index'])->name('timeline')->middleware('can:read-project-timeline');
        Route::get('data', [TimelineController::class, 'timeline'])->name('tasks.data');
        // Route::get('/tambah', [TaskController::class, 'create'])->name('tasks.add')->middleware('can:create-tasks');
        // Route::post('store', [TaskController::class, 'store'])->name('tasks.store');
        // Route::get('/edit/{id}', [TaskController::class, 'show'])->name('tasks.edit')->middleware('can:update-tasks');
        // Route::put('/update/{id}', [TaskController::class, 'update'])->name('tasks.update');
        // Route::delete('/delete/{id}', [TaskController::class, 'destroy'])->name('tasks.delete')->middleware('can:delete-tasks');
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
            Route::get('/', [SettingAplicationController::class, 'index'])->name('aplication')->middleware('can:setting-aplication');
            Route::post('/update', [SettingAplicationController::class, 'update'])->name('aplication.update');
        });

        // Log Activity
        Route::prefix('log')->group(function () {
            Route::get('/', [LogController::class, 'index'])->name('log')->middleware('can:read-logs');
            Route::get('getData', [LogController::class, 'getData'])->name('log.getdata');
            Route::post('clean', [LogController::class, 'cleanlog'])->name('log.clean')->middleware('can:clean-logs');
        });

        Route::prefix('profile')->group(function () {
            Route::get('/{id}', [ProfileController::class, 'index'])->name('setting.profile')->middleware('can:setting-profile');
            Route::put('/update/{id}', [ProfileController::class, 'update'])->name('setting.profile.update');
        });
    });
});
