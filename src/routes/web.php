<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest; // メール認証機能
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 本番では外す、一時的な設定
Route::get('/email/verify', function () {
    return view('auth.verify-email');
});

require __DIR__.'/auth.php';
// ログインできていないとhttp://localhost/loginに強制的にバックする



// 管理者ログイン前
Route::get('admin/login', [Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [Admin\Auth\LoginController::class, 'login']);

// 管理者ログイン後（要ミドルウェア）
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('attendances', [Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('staffs', [Admin\StaffController::class, 'index'])->name('staffs.index');
    Route::get('stamp-correction-requests', [Admin\RequestController::class, 'index'])->name('stamp_correction_requests.index');
    Route::post('logout', [Admin\Auth\LoginController::class, 'logout'])->name('logout');
});