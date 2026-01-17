<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
// // トップページ
// Route::get('/login', function () {
//     return view('auth.login');
// });

// 誘導画面（メール未認証時に表示）
Route::get('/email/verify', function () {
    return view('user.verification.notice');
})->middleware('auth')->name('verification.notice');

// メールの認証リンク（クリック後の処理）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect(route('user.attendance.index'));
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// メール認証のルート
Route::middleware(['auth', 'verified'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {

        Route::get(
            '/attendance/index',
            [AttendanceController::class, 'index']
        )->name('attendance.index');
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])
        ->name('attendance.clockIn');
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
        ->name('attendance.clockOut');

        Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])
        ->name('attendance.breakStart');

        Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])
        ->name('attendance.breakEnd');

        Route::get('/attendance/list', [AttendanceController::class, 'monthly'])
            ->name('attendance.list');

        Route::get('/attendance/detail/{attendance}', [AttendanceController::class, 'show'])
            ->name('attendance.show');

        Route::post('/attendance/{attendance}/request',[AttendanceRequestController::class, 'store'])
            ->name('attendance.request.store');

        Route::prefix('stamp_correction_request')
            ->name('stamp_correction_request.')
            ->group(function () {

                Route::get('/list', [AttendanceRequestController::class, 'index'])
                    ->name('list');
            });
    });

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
            ->name('login');
    });

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Route::get('/dashboard', function () {
        //     return view('admin.dashboard');
        // })->name('dashboard');

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/attendance/{attendance}', [AdminAttendanceController::class, 'show'])
            ->name('attendance.show');

        Route::patch('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])
            ->name('attendance.update');
    });

Route::get('/', function () {
    return redirect()->route('login');
});