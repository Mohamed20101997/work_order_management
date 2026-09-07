<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PartUsageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
    Route::get('forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'email'])
        ->middleware('throttle:6,1')->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'update'])
        ->middleware('throttle:6,1')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('companies', CompanyController::class)->except('destroy');

    Route::resource('assets', AssetController::class);
    Route::post('assets/{asset}/status', [AssetController::class, 'updateStatus'])->name('assets.status');

    Route::resource('work-orders', WorkOrderController::class)->except('destroy');
    Route::post('work-orders/{work_order}/assign', [WorkOrderController::class, 'assign'])->name('work-orders.assign');
    Route::post('work-orders/{work_order}/status', [WorkOrderController::class, 'updateStatus'])->name('work-orders.status');
    Route::post('work-orders/{work_order}/parts', [PartUsageController::class, 'store'])->name('work-orders.parts.store');

    Route::resource('inspections', InspectionController::class)->except('destroy');

    Route::resource('parts', PartController::class)->except(['show', 'destroy']);

    Route::post('attachments/{type}/{id}', [AttachmentController::class, 'store'])
        ->middleware('throttle:30,1')->name('attachments.store');
    Route::get('attachments/{type}/{id}', [AttachmentController::class, 'index'])
        ->middleware('throttle:30,1')->name('attachments.index');
    Route::delete('attachments/media/{mediaId}', [AttachmentController::class, 'destroyByMediaId'])
        ->name('attachments.destroyMedia');
    Route::get('attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
});
