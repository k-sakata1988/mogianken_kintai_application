<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

// // トップページ
// Route::get('/login', function () {
//     return view('auth.login');
// });

// 誘導画面（メール未認証時に表示）
Route::get('/email/verify', function () {
    return view('verification.notice'); // 自作した誘導画面
})->middleware('auth')->name('verification.notice');

// メールの認証リンク（クリック後の処理）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect(route('attendance.index')); // 認証完了後の遷移先
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// メール認証が必要な保護ルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance/index',[AttendanceController::class,'index'])->name('attendance.index');
});
